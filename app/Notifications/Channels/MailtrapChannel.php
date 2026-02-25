<?php

namespace App\Notifications\Channels;

use App\Services\MailtrapService;
use Exception;
use Illuminate\Notifications\Notification;

class MailtrapChannel
{
    protected MailtrapService $mailtrapService;

    public function __construct(MailtrapService $mailtrapService)
    {
        $this->mailtrapService = $mailtrapService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @return void
     *
     * @throws Exception
     */
    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toMailtrap')) {
            throw new Exception('Notification must implement toMailtrap method');
        }

        /** @var \App\Notifications\Messages\MailtrapMessage $message */
        $message = $notification->toMailtrap($notifiable);

        // Get recipient email
        $to = $notifiable->routeNotificationFor('mailtrap', $notification)
            ?? $notifiable->email
            ?? $notifiable->routeNotificationFor('mail', $notification);

        if (empty($to)) {
            throw new Exception('No email address found for notifiable');
        }

        // Send email via Mailtrap service
        $this->mailtrapService->send(
            to: $to,
            subject: $message->subject ?? 'Notification',
            text: $message->text ?? '',
            html: $message->html ?? null,
            from: $message->from ?? null,
            category: $message->category ?? null,
            attachments: $message->attachments ?? []
        );
    }
}
