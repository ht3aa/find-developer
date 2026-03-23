<?php

namespace App\Notifications;

use App\Notifications\Channels\MailtrapBulkChannel;
use App\Notifications\Messages\MailtrapBulkMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MailtrapBulkNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public array $emails,
        public string $subject,
        public string $message,
        public ?string $category = null
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [MailtrapBulkChannel::class];
    }

    /**
     * Get the mailtrap bulk representation of the notification.
     */
    public function toMailtrapBulk(object $notifiable): MailtrapBulkMessage
    {
        return MailtrapBulkMessage::create()
            ->emails($this->emails)
            ->subject($this->subject)
            ->text($this->message)
            ->html($this->getHtmlMessage())
            ->category($this->category ?? 'Bulk Notification');
    }

    /**
     * Get the HTML representation of the message.
     */
    protected function getHtmlMessage(): string
    {
        return view('emails.mailtrap-notification', [
            'subject' => $this->subject,
            'message' => $this->message,
        ])->render();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'emails' => $this->emails,
            'subject' => $this->subject,
            'message' => $this->message,
            'category' => $this->category,
        ];
    }
}
