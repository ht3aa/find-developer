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
     */
    public function __construct(
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
            ->html($this->getHtmlMessage())
            ->category($this->category ?? 'Notification');
    }

    /**
     * Get the HTML representation of the message.
     */
    protected function getHtmlMessage(): string
    {
        return "<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <link rel='icon' href='" . e(asset('logo.svg')) . "' type='image/svg+xml'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color:rgb(0, 49, 173); color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>" . htmlspecialchars($this->subject) . "</h1>
        </div>
        <div class='content'>
            <p>" . nl2br(htmlspecialchars($this->message)) . "</p>
        </div>
        <div class='footer'>
            <p>This email was sent via Mailtrap</p>
        </div>
    </div>
</body>
</html>";
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
