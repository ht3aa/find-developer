<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MailtrapService
{
    protected string $apiUrl;

    protected string $apiToken;

    protected ?string $defaultFromEmail;

    protected ?string $defaultFromName;

    public function __construct()
    {
        $this->apiUrl = 'https://send.api.mailtrap.io/api/send';
        $this->apiToken = config('mail.mailers.smtp.password', env('MAILTRAP_SECRET'));
        $this->defaultFromEmail = config('mail.from.address');
        $this->defaultFromName = config('mail.from.name');
    }

    /**
     * Send an email via Mailtrap API
     *
     * @param  string|array  $to  Email address(es) to send to
     * @param  string  $subject  Email subject
     * @param  string  $text  Plain text content
     * @param  string|null  $html  HTML content (optional)
     * @param  array  $from  ['email' => string, 'name' => string] (optional, uses config defaults)
     * @param  string|null  $category  Category for tracking (optional)
     * @param  array  $attachments  Array of attachment paths (optional)
     *
     * @throws Exception
     */
    public function send(
        string|array $to,
        string $subject,
        string $text,
        ?string $html = null,
        ?array $from = null,
        ?string $category = null,
        array $attachments = []
    ): array {
        if (empty($this->apiToken)) {
            throw new Exception('Mailtrap API token is not configured. Please set MAILTRAP_SECRET in your .env file.');
        }

        // Normalize recipients to array format
        $recipients = is_array($to) ? $to : [$to];
        $toArray = array_map(function ($email) {
            return is_array($email) ? $email : ['email' => $email];
        }, $recipients);

        // Prepare from data
        $fromData = $from ?? [
            'email' => $this->defaultFromEmail,
            'name' => $this->defaultFromName,
        ];

        // Build payload
        $payload = [
            'from' => $fromData,
            'to' => $toArray,
            'subject' => $subject,
            'text' => $text,
        ];

        // Add HTML if provided
        if ($html !== null) {
            $payload['html'] = $html;
        }

        // Add category if provided
        if ($category !== null) {
            $payload['category'] = $category;
        }

        // Add attachments if provided
        if (! empty($attachments)) {
            $payload['attachments'] = $this->prepareAttachments($attachments);
        }

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, $payload);

            if ($response->successful()) {
                Log::info('Mailtrap email sent successfully', [
                    'to' => $to,
                    'subject' => $subject,
                ]);

                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Mailtrap API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'to' => $to,
                'subject' => $subject,
            ]);

            throw new Exception('Failed to send email via Mailtrap: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Mailtrap service exception', [
                'message' => $e->getMessage(),
                'to' => $to,
                'subject' => $subject,
            ]);

            throw $e;
        }
    }

    /**
     * Prepare attachments for Mailtrap API
     *
     * @param  array  $attachments  Array of file paths
     */
    protected function prepareAttachments(array $attachments): array
    {
        $prepared = [];

        foreach ($attachments as $attachment) {
            if (is_string($attachment) && file_exists($attachment)) {
                $prepared[] = [
                    'content' => base64_encode(file_get_contents($attachment)),
                    'filename' => basename($attachment),
                    'type' => mime_content_type($attachment),
                    'disposition' => 'attachment',
                ];
            } elseif (is_array($attachment)) {
                // Support custom attachment format
                $prepared[] = $attachment;
            }
        }

        return $prepared;
    }

    /**
     * Send bulk emails via Mailtrap Bulk API
     *
     * @param  array  $to  Array of email addresses to send to
     * @param  string  $subject  Email subject
     * @param  string  $text  Plain text content
     * @param  string|null  $html  HTML content (optional)
     * @param  array  $from  ['email' => string, 'name' => string] (optional, uses config defaults)
     * @param  string|null  $category  Category for tracking (optional)
     *
     * @throws Exception
     */
    public function sendBulk(
        array $to,
        string $subject,
        string $text,
        ?string $html = null,
        ?array $from = null,
        ?string $category = null
    ): array {
        if (empty($this->apiToken)) {
            throw new Exception('Mailtrap API token is not configured. Please set MAILTRAP_SECRET in your .env file.');
        }

        if (empty($to)) {
            throw new Exception('No recipients provided for bulk email.');
        }

        // Normalize recipients to array format
        $toArray = array_map(function ($email) {
            return is_array($email) ? $email : ['email' => $email];
        }, $to);

        // Prepare from data
        $fromData = $from ?? [
            'email' => $this->defaultFromEmail,
            'name' => $this->defaultFromName,
        ];

        // Build payload for bulk API
        $payload = [
            'from' => $fromData,
            'to' => $toArray,
            'subject' => $subject,
            'text' => $text,
        ];

        // Add HTML if provided
        if ($html !== null) {
            $payload['html'] = $html;
        }

        // Add category if provided
        if ($category !== null) {
            $payload['category'] = $category;
        }

        try {
            $bulkApiUrl = 'https://bulk.api.mailtrap.io/api/send';

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($bulkApiUrl, $payload);

            if ($response->successful()) {
                Log::info('Mailtrap bulk email sent successfully', [
                    'recipient_count' => count($to),
                    'subject' => $subject,
                ]);

                return [
                    'success' => true,
                    'data' => $response->json(),
                    'recipient_count' => count($to),
                ];
            }

            Log::error('Mailtrap Bulk API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'recipient_count' => count($to),
                'subject' => $subject,
            ]);

            throw new Exception('Failed to send bulk email via Mailtrap: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Mailtrap bulk service exception', [
                'message' => $e->getMessage(),
                'recipient_count' => count($to),
                'subject' => $subject,
            ]);

            throw $e;
        }
    }

    /**
     * Send a test email
     *
     * @param  string  $to  Email address
     *
     * @throws Exception
     */
    public function sendTest(string $to): array
    {
        return $this->send(
            to: $to,
            subject: 'Mailtrap Test Email',
            text: 'This is a test email sent via Mailtrap API.',
            category: 'Test'
        );
    }
}
