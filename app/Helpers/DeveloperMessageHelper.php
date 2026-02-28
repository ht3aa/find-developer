<?php

namespace App\Helpers;

class DeveloperMessageHelper
{
    /**
     * Generate the email message body for "User Credentials Created" notifications.
     */
    public static function userCredentialsCreatedMessage(string $developerName, string $email, string $password): string
    {
        $message = "Hello {$developerName}\n\n";
        $message .= "Your user account has been created. Here are your credentials:\n\n";
        $message .= "Email: {$email}\n";
        $message .= "Password: {$password}\n\n";
        $message .= "You can edit your information and do more actions via the admin dashboard:\n";
        $message .= config('app.url')."/admin\n\n";
        $message .= "You can now also recommend other developers. Please use the recommendation feature only on the developers you well know.\n\n";
        $message .= "Best Regards\n";
        $message .= 'Hasan Tahseen an Admin in '.config('app.url').' platform';

        return $message;
    }

    /**
     * Header text for bulk emails sent to developers.
     */
    public static function bulkMessageHeader(string $developerName): string
    {
        $name = config('app.name');

        return "Hello {$developerName},\n\n"
            ."You are receiving this message from the {$name} platform.\n\n"
            ."---\n\n";
    }

    /**
     * Footer text for bulk emails sent to developers.
     */
    public static function bulkMessageFooter(): string
    {
        $url = config('app.url');

        return "\n\n---\n\n"
            ."Best Regards,\n"
            .'The team at '.$url;
    }

    /**
     * Build the full bulk email message body with header, content and footer.
     */
    public static function bulkMessageBody(string $content, string $developerName): string
    {
        return self::bulkMessageHeader($developerName).trim($content).self::bulkMessageFooter();
    }
}
