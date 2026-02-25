<?php

namespace App\Notifications\Channels;

use App\Services\MailtrapService;
use Exception;
use Illuminate\Notifications\Notification;

class MailtrapBulkChannel
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
        if (! method_exists($notification, 'toMailtrapBulk')) {
            throw new Exception('Notification must implement toMailtrapBulk method');
        }

        /** @var \App\Notifications\Messages\MailtrapBulkMessage $message */
        $message = $notification->toMailtrapBulk($notifiable);

        // Get recipient emails - for bulk, we expect an array of emails
        $emails = $message->emails ?? [];

        if (empty($emails)) {
            // Fallback: try to get email from notifiable if it's a single model
            $email = $notifiable->routeNotificationFor('mailtrap', $notification)
                ?? $notifiable->email
                ?? $notifiable->routeNotificationFor('mail', $notification);

            if ($email) {
                $emails = [$email];
            }
        }

        if (empty($emails)) {
            throw new Exception('No email addresses found for bulk notification');
        }

        // Send bulk email via Mailtrap service
        $this->mailtrapService->sendBulk(
            to: $emails,
            subject: $message->subject ?? 'Notification',
            text: $message->text ?? '',
            html: $message->html ?? null,
            from: $message->from ?? null,
            category: $message->category ?? null
        );
    }
}
