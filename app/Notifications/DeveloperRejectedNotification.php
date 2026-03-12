<?php

namespace App\Notifications;

use App\Models\Developer;
use App\Notifications\Channels\MailtrapChannel;
use App\Notifications\Messages\MailtrapMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DeveloperRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Developer $developer,
        public string $reason
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [MailtrapChannel::class];
    }

    public function toMailtrap(object $notifiable): MailtrapMessage
    {
        $html = view('emails.developer-rejected', [
            'developer' => $this->developer,
            'reason' => $this->reason,
        ])->render();

        return MailtrapMessage::create()
            ->subject('Developer Profile Rejected')
            ->html($html)
            ->category('Developer Rejected');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'developer_id' => $this->developer->id,
        ];
    }
}
