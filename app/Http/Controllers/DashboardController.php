<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match (true) {
            $user->isAdmin() => $this->adminDashboard(),
            $user->isItLead() => $this->leadDashboard(),
            $user->isItStaff() => $this->staffDashboard(),
            default => $this->userDashboard(),
        };
    }

    protected function userDashboard()
    {
        $user = Auth::user();

        $myTickets = Ticket::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'total' => Ticket::where('user_id', $user->id)->count(),
            'open' => Ticket::where('user_id', $user->id)->where('status', TicketStatus::Open)->count(),
            'in_progress' => Ticket::where('user_id', $user->id)->where('status', TicketStatus::InProgress)->count(),
            'closed' => Ticket::where('user_id', $user->id)->where('status', TicketStatus::Closed)->count(),
        ];

        return view('dashboard.user', compact('myTickets', 'stats'));
    }

    protected function staffDashboard()
    {
        $user = Auth::user();

        $availableTickets = Ticket::where('status', TicketStatus::Open)
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        $myAssignedTickets = Ticket::where('assigned_to', $user->id)
            ->whereNotIn('status', [TicketStatus::Closed])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'available' => Ticket::where('status', TicketStatus::Open)->count(),
            'my_active' => Ticket::where('assigned_to', $user->id)->whereNotIn('status', [TicketStatus::Closed])->count(),
            'my_open' => Ticket::where('assigned_to', $user->id)->where('status', [TicketStatus::Open])->count(),
            'my_completed' => Ticket::where('assigned_to', $user->id)->where('status', TicketStatus::Closed)->count(),
            'my_overdue' => Ticket::where('assigned_to', $user->id)->where('is_sla_breached', true)->whereNotIn('status', [TicketStatus::Closed])->count(),
        ];

        return view('dashboard.staff', compact('availableTickets', 'myAssignedTickets', 'stats'));
    }

    protected function leadDashboard()
    {
        $pendingReview = Ticket::where('status', TicketStatus::PendingReview)
            ->with(['user', 'assignee', 'category'])
            ->orderBy('created_at', 'asc')
            ->get();

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Team KPI
        $teamMembers = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['it-staff', 'it-lead']))
            ->withCount([
                'assignedTickets as tickets_completed' => fn ($q) => $q->where('status', TicketStatus::Closed)->whereBetween('closed_at', [$startOfMonth, $endOfMonth]),
                'assignedTickets as tickets_active' => fn ($q) => $q->whereNotIn('status', [TicketStatus::Closed]),
                'assignedTickets as tickets_overdue' => fn ($q) => $q->where('is_sla_breached', true)->whereNotIn('status', [TicketStatus::Closed]),
            ])
            ->get();

        $stats = [
            'pending_review' => $pendingReview->count(),
            'total_month' => Ticket::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'closed_month' => Ticket::where('status', TicketStatus::Closed)->whereBetween('closed_at', [$startOfMonth, $endOfMonth])->count(),
            'sla_breach_month' => Ticket::where('is_sla_breached', true)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'overdue_active' => Ticket::where('is_sla_breached', true)->whereNotIn('status', [TicketStatus::Closed])->count(),
        ];

        // SLA Compliance
        $totalClosed = max(1, $stats['closed_month']);
        $slaCompliant = Ticket::where('status', TicketStatus::Closed)
            ->where('is_sla_breached', false)
            ->whereBetween('closed_at', [$startOfMonth, $endOfMonth])
            ->count();
        $stats['sla_compliance'] = round(($slaCompliant / $totalClosed) * 100, 1);

        return view('dashboard.lead', compact('pendingReview', 'teamMembers', 'stats'));
    }

    protected function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_tickets' => Ticket::count(),
            'open_tickets' => Ticket::where('status', TicketStatus::Open)->count(),
            'in_progress' => Ticket::whereIn('status', [TicketStatus::InProgress, TicketStatus::Assigned])->count(),
            'closed_tickets' => Ticket::where('status', TicketStatus::Closed)->count(),
            'overdue_tickets' => Ticket::where('is_sla_breached', true)->whereNotIn('status', [TicketStatus::Closed])->count(),
        ];

        // Monthly ticket trend (last 6 months)
        $monthlyTrend = Ticket::select(
            DB::raw('EXTRACT(MONTH FROM created_at) as month'),
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $recentTickets = Ticket::with(['user', 'assignee', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact('stats', 'monthlyTrend', 'recentTickets'));
    }
}
