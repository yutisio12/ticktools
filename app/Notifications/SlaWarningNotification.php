<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SlaWarningNotification extends Notification implements ShouldQueue
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
            ->subject("SLA Warning/Breach: {$this->ticket->ticket_number}")
            ->error()
            ->line("Ticket {$this->ticket->ticket_number} SLA is nearing breach or has breached.")
            ->line("Deadline: {$this->ticket->sla_deadline?->format('Y-m-d H:i:s')}")
            ->action('Take Action Now', route('tickets.show', $this->ticket));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'message' => "SLA Warning for ticket {$this->ticket->ticket_number}",
            'type' => 'sla_warning'
        ];
    }
    
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'message' => "SLA Warning: Ticket {$this->ticket->ticket_number}.",
        ]);
    }
}
