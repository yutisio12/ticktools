<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TicketCreatedNotification extends Notification implements ShouldQueue
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
            ->subject("New Ticket Created: {$this->ticket->ticket_number}")
            ->greeting("Hello,")
            ->line("A new ticket has been created by {$this->ticket->user->name}.")
            ->line("Category: {$this->ticket->category->name}")
            ->line("Title: {$this->ticket->title}")
            ->action('View Ticket', route('tickets.show', $this->ticket))
            ->line('Thank you for using our IT Helpdesk system!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title' => $this->ticket->title,
            'message' => "New ticket created by {$this->ticket->user->name}",
            'type' => 'created'
        ];
    }
    
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'message' => "New ticket {$this->ticket->ticket_number} needs attention.",
        ]);
    }
}
