<?php

namespace App\Notifications;

use App\Models\Developer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DeveloperApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Developer $developer
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Developer Profile Approved')
            ->view('emails.developer-approved', [
                'developer' => $this->developer,
            ]);
    }
}
