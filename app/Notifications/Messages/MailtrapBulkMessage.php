<?php

namespace App\Notifications\Messages;

class MailtrapBulkMessage
{
    public ?string $subject = null;

    public ?string $text = null;

    public ?string $html = null;

    public ?array $from = null;

    public ?string $category = null;

    public array $emails = [];

    /**
     * Create a new message instance.
     */
    public static function create(): self
    {
        return new self;
    }

    /**
     * Set the email subject.
     */
    public function subject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set the plain text content.
     */
    public function text(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Set the HTML content.
     */
    public function html(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Set the from email and name.
     */
    public function from(string $email, ?string $name = null): self
    {
        $this->from = [
            'email' => $email,
            'name' => $name,
        ];

        return $this;
    }

    /**
     * Set the category for tracking.
     */
    public function category(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Set the recipient emails (for bulk sending).
     */
    public function emails(array $emails): self
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Add a single email to the recipients list.
     */
    public function addEmail(string $email): self
    {
        if (! in_array($email, $this->emails)) {
            $this->emails[] = $email;
        }

        return $this;
    }
}
