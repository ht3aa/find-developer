<?php

namespace App\Notifications;

use App\Models\Hackathon;
use App\Models\HackathonSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class HackathonSubscriberConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public HackathonSubscriber $subscriber,
        public Hackathon $hackathon
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Hackathon registration confirmed: '.$this->hackathon->title)
            ->view('emails.hackathon-subscriber-confirmed', [
                'subscriber' => $this->subscriber,
                'hackathon' => $this->hackathon,
                'developer' => $this->subscriber->developer,
            ]);
    }
}
