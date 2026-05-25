<?php

namespace App\Services;

use App\Enums\Impact;
use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function __construct(
        protected SlaService $slaService,
        protected TicketWeightService $weightService,
    ) {}

    /**
     * Create a new ticket from user input
     */
    public function create(array $data): Ticket
    {
        return DB::transaction(function () use ($data) {
            $ticket = Ticket::create([
                'ticket_number' => Ticket::generateTicketNumber(),
                'user_id' => Auth::id(),
                'category_id' => $data['category_id'],
                'subcategory_id' => $data['subcategory_id'] ?? null,
                'asset_id' => $data['asset_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => TicketStatus::Open,
            ]);

            $this->logHistory($ticket, 'created', null, null, null, 'Ticket created');

            return $ticket;
        });
    }

    /**
     * IT Staff claims a ticket
     */
    public function claim(Ticket $ticket): Ticket
    {
        return DB::transaction(function () use ($ticket) {
            $oldStatus = $ticket->status;

            $ticket->update([
                'assigned_to' => Auth::id(),
                'status' => TicketStatus::Assigned,
            ]);

            $this->logHistory($ticket, 'claimed', 'status', $oldStatus->value, TicketStatus::Assigned->value);
            $this->logHistory($ticket, 'assigned', 'assigned_to', null, Auth::user()->name);

            return $ticket->fresh();
        });
    }

    /**
     * IT Staff starts working on a ticket
     */
    public function startWork(Ticket $ticket): Ticket
    {
        return DB::transaction(function () use ($ticket) {
            $oldStatus = $ticket->status;

            $ticket->update([
                'status' => TicketStatus::InProgress,
                'started_at' => now(),
            ]);

            $this->logHistory($ticket, 'status_changed', 'status', $oldStatus->value, TicketStatus::InProgress->value);

            return $ticket->fresh();
        });
    }

    /**
     * IT Staff sets priority and impact (calculates SLA + weight)
     */
    public function setPriorityAndImpact(Ticket $ticket, string $priority, string $impact): Ticket
    {
        return DB::transaction(function () use ($ticket, $priority, $impact) {
            $priorityEnum = Priority::from($priority);
            $impactEnum = Impact::from($impact);

            // Calculate SLA deadline
            $slaDeadline = $this->slaService->calculateDeadline($ticket->category_id, $priorityEnum);

            // Calculate weight
            $weight = $this->weightService->calculate($ticket->category, $priorityEnum, $impactEnum);

            $ticket->update([
                'priority' => $priorityEnum,
                'impact' => $impactEnum,
                'sla_deadline' => $slaDeadline,
                'weight_score' => $weight,
            ]);

            $this->logHistory($ticket, 'priority_set', 'priority', null, $priorityEnum->value);
            $this->logHistory($ticket, 'impact_set', 'impact', null, $impactEnum->value);

            return $ticket->fresh();
        });
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Ticket $ticket, TicketStatus $newStatus): Ticket
    {
        return DB::transaction(function () use ($ticket, $newStatus) {
            $oldStatus = $ticket->status;

            $updateData = ['status' => $newStatus];

            if ($newStatus === TicketStatus::InProgress && !$ticket->started_at) {
                $updateData['started_at'] = now();
            }

            $ticket->update($updateData);

            // Check SLA breach
            if ($ticket->isOverdue() && !$ticket->is_sla_breached) {
                $ticket->update(['is_sla_breached' => true]);
            }

            $this->logHistory($ticket, 'status_changed', 'status', $oldStatus->value, $newStatus->value);

            return $ticket->fresh();
        });
    }

    /**
     * Submit ticket for review (IT Staff fills resolution)
     */
    public function submitForReview(Ticket $ticket, array $data): Ticket
    {
        return DB::transaction(function () use ($ticket, $data) {
            $oldStatus = $ticket->status;

            $ticket->update([
                'status' => TicketStatus::PendingReview,
                'resolution' => $data['resolution'],
                'root_cause' => $data['root_cause'],
                'prevention' => $data['prevention'] ?? null,
                'resolved_at' => now(),
            ]);

            // Check SLA breach
            if ($ticket->isOverdue()) {
                $ticket->update(['is_sla_breached' => true]);
            }

            $this->logHistory($ticket, 'submitted_for_review', 'status', $oldStatus->value, TicketStatus::PendingReview->value);

            return $ticket->fresh();
        });
    }

    /**
     * IT Lead approves a ticket
     */
    public function approve(Ticket $ticket, ?string $notes = null): Ticket
    {
        return DB::transaction(function () use ($ticket, $notes) {
            $oldStatus = $ticket->status;

            $ticket->update([
                'status' => TicketStatus::Closed,
                'reviewed_by' => Auth::id(),
                'review_notes' => $notes,
                'closed_at' => now(),
            ]);

            $this->logHistory($ticket, 'approved', 'status', $oldStatus->value, TicketStatus::Closed->value, $notes);

            return $ticket->fresh();
        });
    }

    /**
     * IT Lead returns a ticket
     */
    public function returnTicket(Ticket $ticket, string $notes): Ticket
    {
        return DB::transaction(function () use ($ticket, $notes) {
            $oldStatus = $ticket->status;

            $ticket->update([
                'status' => TicketStatus::Returned,
                'reviewed_by' => Auth::id(),
                'review_notes' => $notes,
                'resolved_at' => null,
            ]);

            $this->logHistory($ticket, 'returned', 'status', $oldStatus->value, TicketStatus::Returned->value, $notes);

            return $ticket->fresh();
        });
    }

    /**
     * User reopens a ticket
     */
    public function reopen(Ticket $ticket, string $reason): Ticket
    {
        return DB::transaction(function () use ($ticket, $reason) {
            $oldStatus = $ticket->status;

            $ticket->update([
                'status' => TicketStatus::Open,
                'reopened_at' => now(),
                'reopen_count' => $ticket->reopen_count + 1,
                'closed_at' => null,
                'resolved_at' => null,
                'resolution' => null,
                'root_cause' => null,
                'prevention' => null,
                'reviewed_by' => null,
                'review_notes' => null,
            ]);

            $this->logHistory($ticket, 'reopened', 'status', $oldStatus->value, TicketStatus::Open->value, $reason);

            return $ticket->fresh();
        });
    }

    /**
     * Log ticket history
     */
    protected function logHistory(Ticket $ticket, string $action, ?string $field, ?string $oldValue, ?string $newValue, ?string $notes = null): void
    {
        TicketHistory::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'field_changed' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'notes' => $notes,
        ]);
    }
}
