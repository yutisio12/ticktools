<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TicketAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Ticket $ticket)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Ticket Assigned: {$this->ticket->ticket_number}")
            ->line("Ticket {$this->ticket->ticket_number} has been assigned to you.")
            ->line("Title: {$this->ticket->title}")
            ->action('View Ticket', route('tickets.show', $this->ticket));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'message' => "Ticket {$this->ticket->ticket_number} assigned to you",
            'type' => 'assigned'
        ];
    }
    
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'message' => "You've been assigned to ticket {$this->ticket->ticket_number}.",
        ]);
    }
}
