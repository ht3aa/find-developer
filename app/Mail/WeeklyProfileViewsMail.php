<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyProfileViewsMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $developerName,
        public readonly int $viewCount,
        public readonly string $profileUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your weekly profile stats – Find Developer',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-profile-views',
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
