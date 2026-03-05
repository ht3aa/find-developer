<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeveloperOfferApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $developerName,
        public readonly string $companyName,
        public readonly string $jobTitleName,
        public readonly ?string $salaryRange,
        public readonly ?string $workType,
        public readonly string $contactEmail,
        public readonly ?string $senderMessage
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'A Company Is Looking for Developers Like You',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.developer-offer-approved',
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
