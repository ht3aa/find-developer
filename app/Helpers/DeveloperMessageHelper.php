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
}
