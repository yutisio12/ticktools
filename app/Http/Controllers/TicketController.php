<?php

namespace App\Http\Controllers;

use App\Enums\Impact;
use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketAttachment;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct(
        protected TicketService $ticketService,
    ) {}

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Ticket::with(['user', 'assignee', 'category']);

        // Role-based filtering
        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $tickets = $query->paginate(15);

        $categories = Category::where('is_active', true)->get();

        return view('tickets.index', compact('tickets', 'categories'));
    }

    public function create()
    {
        $categories = Category::with('subcategories')->where('is_active', true)->get();
        $assets = Asset::where('is_active', true)->orderBy('name')->get();

        return view('tickets.create', compact('categories', 'assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'asset_id' => 'nullable|exists:assets,id',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $ticket = $this->ticketService->create($request->only([
            'title', 'description', 'category_id', 'subcategory_id', 'asset_id',
        ]));

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/' . $ticket->id, 'public');
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'filename' => $file->hashName(),
                    'original_name' => $file->getClientOriginalName(),
                    'filepath' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket created successfully: ' . $ticket->ticket_number);
    }

    public function show(Ticket $ticket)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization check
        if ($user->isUser() && $ticket->user_id !== $user->id) {
            abort(403);
        }

        $ticket->load([
            'user', 'assignee', 'category', 'subcategory', 'asset',
            'attachments.uploader', 'activities.user', 'histories.user', 'reviewer',
        ]);

        return view('tickets.show', compact('ticket'));
    }

    public function claim(Ticket $ticket)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isItStaff() && !$user->isItLead() && !$user->isAdmin()) {
            abort(403);
        }

        if ($ticket->status !== TicketStatus::Open) {
            return back()->with('error', 'This ticket cannot be claimed.');
        }

        $this->ticketService->claim($ticket);

        return back()->with('success', 'Ticket claimed successfully!');
    }

    public function startWork(Ticket $ticket)
    {
        if ($ticket->assigned_to !== Auth::id()) {
            abort(403);
        }

        $this->ticketService->startWork($ticket);

        return back()->with('success', 'Work started on ticket.');
    }

    public function updatePriority(Request $request, Ticket $ticket)
    {
        $request->validate([
            'priority' => 'required|in:' . implode(',', array_column(Priority::cases(), 'value')),
            'impact' => 'required|in:' . implode(',', array_column(Impact::cases(), 'value')),
        ]);

        $this->ticketService->setPriorityAndImpact($ticket, $request->priority, $request->impact);

        return back()->with('success', 'Priority and impact updated.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_column(TicketStatus::cases(), 'value')),
        ]);

        $this->ticketService->updateStatus($ticket, TicketStatus::from($request->status));

        return back()->with('success', 'Ticket status updated.');
    }

    public function addActivity(Request $request, Ticket $ticket)
    {
        $request->validate([
            'activity_note' => 'required|string',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'duration_minutes' => 'required|integer|min:1',
            'activity_date' => 'required|date',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $data = $request->only(['activity_note', 'progress_percentage', 'duration_minutes', 'activity_date']);
        $data['ticket_id'] = $ticket->id;
        $data['user_id'] = Auth::id();

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('activities/' . $ticket->id, 'public');
        }

        TicketActivity::create($data);

        return back()->with('success', 'Activity logged successfully.');
    }

    public function submitForReview(Request $request, Ticket $ticket)
    {
        $request->validate([
            'resolution' => 'required|string',
            'root_cause' => 'required|string',
            'prevention' => 'nullable|string',
        ]);

        $this->ticketService->submitForReview($ticket, $request->only(['resolution', 'root_cause', 'prevention']));

        return back()->with('success', 'Ticket submitted for review.');
    }

    public function approve(Request $request, Ticket $ticket)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isItLead() && !$user->isAdmin()) {
            abort(403);
        }

        $this->ticketService->approve($ticket, $request->review_notes);

        return back()->with('success', 'Ticket approved and closed.');
    }

    public function returnTicket(Request $request, Ticket $ticket)
    {
        $request->validate([
            'review_notes' => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isItLead() && !$user->isAdmin()) {
            abort(403);
        }

        $this->ticketService->returnTicket($ticket, $request->review_notes);

        return back()->with('success', 'Ticket returned for revision.');
    }

    public function reopen(Request $request, Ticket $ticket)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        if (!$ticket->canBeReopened()) {
            return back()->with('error', 'This ticket cannot be reopened.');
        }

        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $this->ticketService->reopen($ticket, $request->reason);

        return back()->with('success', 'Ticket reopened successfully.');
    }

    public function getSubcategories(int $categoryId)
    {
        $subcategories = Subcategory::where('category_id', $categoryId)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return response()->json($subcategories);
    }
}
