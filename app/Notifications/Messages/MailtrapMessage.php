<?php

namespace App\Notifications\Messages;

class MailtrapMessage
{
    public ?string $subject = null;

    public ?string $text = null;

    public ?string $html = null;

    public ?array $from = null;

    public ?string $category = null;

    public array $attachments = [];

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
     * Add an attachment.
     */
    public function attach(string|array $attachment): self
    {
        if (is_array($attachment)) {
            $this->attachments[] = $attachment;
        } else {
            $this->attachments[] = $attachment;
        }

        return $this;
    }

    /**
     * Add multiple attachments.
     */
    public function attachMany(array $attachments): self
    {
        foreach ($attachments as $attachment) {
            $this->attach($attachment);
        }

        return $this;
    }
}
