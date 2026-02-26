<?php

namespace App\Notifications;

use App\Models\Developer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminDeveloperNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Developer $developer,
        public string $subject
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($this->subject)
            ->view('emails.admin-developer-notification', [
                'developer' => $this->developer,
                'subject' => $this->subject,
            ]);
    }
}
