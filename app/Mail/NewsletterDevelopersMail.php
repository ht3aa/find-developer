<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterDevelopersMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{name: string, profile_url: string, job_title: string|null, years_of_experience: int, recommended_by_us: bool}>  $developers
     */
    public function __construct(
        public readonly array $developers
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Developer spotlight – Find Developer',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter-developers',
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
