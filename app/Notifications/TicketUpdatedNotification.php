<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public string $updateType = 'updated'
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Ticket Update: {$this->ticket->ticket_number}")
            ->line("Your ticket {$this->ticket->ticket_number} has been {$this->updateType}.")
            ->line("Status: {$this->ticket->status->label()}")
            ->action('View Updates', route('tickets.show', $this->ticket));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'message' => "Ticket {$this->ticket->ticket_number} has been {$this->updateType}",
            'type' => $this->updateType
        ];
    }
}
