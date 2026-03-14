<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewConversationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $sender
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->sender->name.' sent you a message')
            ->view('emails.new-conversation', [
                'sender' => $this->sender,
                'recipient' => $notifiable,
            ]);
    }
}
