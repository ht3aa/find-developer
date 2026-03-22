<?php

namespace App\Services;

use App\Models\Newsletter;
use App\Notifications\MailtrapNotification;
use Illuminate\Validation\ValidationException;

class NewsletterBulkMailService
{
    /**
     * @param  list<int>  $ids
     *
     * @throws ValidationException
     */
    public function sendToIds(array $ids, string $title, string $body, ?string $category = null): int
    {
        if ($ids === []) {
            throw ValidationException::withMessages([
                'subscriber_ids' => 'Select at least one subscriber.',
            ]);
        }

        $subscribers = Newsletter::query()
            ->whereIn('id', $ids)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($subscribers->isEmpty()) {
            throw ValidationException::withMessages([
                'email' => 'No valid subscriber emails to send to.',
            ]);
        }

        $message = $this->buildMessageBody($body);

        foreach ($subscribers as $subscriber) {
            $subscriber->notify(new MailtrapNotification($title, $message, $category));
        }

        return $subscribers->count();
    }

    /**
     * @throws ValidationException
     */
    public function sendToAll(string $title, string $body, ?string $category = null): int
    {
        $subscribers = Newsletter::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('email')
            ->get();

        if ($subscribers->isEmpty()) {
            throw ValidationException::withMessages([
                'email' => 'No valid subscriber emails to send to.',
            ]);
        }

        $message = $this->buildMessageBody($body);

        foreach ($subscribers as $subscriber) {
            $subscriber->notify(new MailtrapNotification($title, $message, $category));
        }

        return $subscribers->count();
    }

    private function buildMessageBody(string $body): string
    {
        return "Hello,\n\nYou are receiving this message from the ".config('app.name')." platform.\n\n---\n\n"
            .trim($body)
            ."\n\n---\n\nBest Regards,\nThe team at ".config('app.url');
    }
}
