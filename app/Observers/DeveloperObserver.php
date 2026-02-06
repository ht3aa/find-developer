<?php

namespace App\Observers;

use App\Enums\DeveloperStatus;
use App\Models\Developer;
use App\Notifications\MailtrapNotification;
use Illuminate\Support\Str;

class DeveloperObserver
{
    /**
     * Handle the Developer "updated" event.
     */
    public function updated(Developer $developer): void
    {
        // Check if status is dirty and is now APPROVED
        if ($developer->isDirty('status') && $developer->status === DeveloperStatus::APPROVED) {
            // Only send email if developer has an email address
            if (!empty($developer->email)) {
                $message = "Hello {$developer->name}\n\n";
                $message .= "Congratulations! Your developer profile has been approved.\n\n";
                $message .= "Best Regards\n";
                $message .= "Hasan Tahseen an Admin in find-developer.com platform";

                $developer->notify(new MailtrapNotification(
                    subject: 'Developer Profile Approved',
                    message: $message,
                    category: 'Developer Approval'
                ));
            }
        }
    }
    public function saving(Developer $developer): void
    {
        if ($developer->isDirty('name') && empty($developer->slug)) {
            $developer->slug = Str::slug($developer->name);
        }
    }

    public function updating(Developer $developer): void
    {
        if ($developer->isDirty('name') && empty($developer->slug)) {
            $developer->slug = Str::slug($developer->name);
        }
    }
}
