<?php

namespace App\Notifications;

use App\Models\Developer;
use App\Notifications\Channels\MailtrapChannel;
use App\Notifications\Messages\MailtrapMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;

class DeveloperSuspendedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Developer $developer
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
        $html = View::make('emails.developer-suspended', [
            'developer' => $this->developer,
        ])->render();

        return MailtrapMessage::create()
            ->subject('Developer Profile Suspended')
            ->html($html)
            ->category('Developer Suspended');
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
