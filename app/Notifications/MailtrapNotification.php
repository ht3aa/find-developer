<?php

namespace App\Notifications;

use App\Notifications\Channels\MailtrapChannel;
use App\Notifications\Messages\MailtrapMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MailtrapNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  string|null  $html  Optional HTML content. When provided, used instead of message for HTML body.
     */
    public function __construct(
        public string $subject,
        public string $message,
        public ?string $category = null,
        public ?string $html = null
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
        return [MailtrapChannel::class];
    }

    /**
     * Get the mailtrap representation of the notification.
     */
    public function toMailtrap(object $notifiable): MailtrapMessage
    {
        return MailtrapMessage::create()
            ->subject($this->subject)
            ->text($this->message)
            ->html($this->html ?? $this->getHtmlMessage())
            ->category($this->category ?? 'Notification');
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
            'subject' => $this->subject,
            'message' => $this->message,
            'category' => $this->category,
        ];
    }
}
