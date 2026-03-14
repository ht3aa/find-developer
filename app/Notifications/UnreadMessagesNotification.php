<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnreadMessagesNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $unreadCount
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have unread messages')
            ->view('emails.unread-messages', [
                'recipient' => $notifiable,
                'unreadCount' => $this->unreadCount,
            ]);
    }
}
