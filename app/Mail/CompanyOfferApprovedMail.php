<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyOfferApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{name: string, years_of_experience: int|null, job_title: string|null, profile_url: string|null}>  $developers
     */
    public function __construct(
        public readonly array $developers,
        public readonly string $jobTitleName,
        public readonly string $contactEmail
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Developers You May Be Interested In',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-offer-approved',
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
