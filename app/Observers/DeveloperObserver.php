<?php

namespace App\Observers;

use App\Enums\DeveloperStatus;
use App\Enums\UserType;
use App\Helpers\DeveloperMessageHelper;
use App\Models\Developer;
use App\Models\User;
use App\Notifications\MailtrapNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DeveloperObserver
{
    /**
     * Handle the Developer "updated" event.
     */
    public function updated(Developer $developer): void
    {
        // Check if status is dirty and is now APPROVED
        if ($developer->isDirty('status') && $developer->status === DeveloperStatus::APPROVED) {
            // Create a user account if the developer doesn't have one
            if (! $developer->user_id && ! empty($developer->email) && ! User::where('email', $developer->email)->exists()) {
                $password = Str::uuid()->toString();

                $user = User::create([
                    'name' => $developer->name,
                    'email' => $developer->email,
                    'password' => $password,
                    'linkedin_url' => $developer->linkedin_url,
                    'user_type' => UserType::DEVELOPER,
                    'can_access_admin_panel' => true,
                ]);

                $role = Role::where('name', 'developer')->first()
                    ?? Role::where('name', 'like', '%developer%')->first()
                    ?? Role::query()->first();
                if ($role) {
                    $user->assignRole($role);
                }

                Developer::withoutEvents(fn () => $developer->update(['user_id' => $user->id]));

                try {
                    $developer->notify(new MailtrapNotification(
                        subject: 'User Credentials Created',
                        message: DeveloperMessageHelper::userCredentialsCreatedMessage($developer->name, $developer->email, $password),
                        category: 'User Credentials'
                    ));
                } catch (\Throwable $e) {
                    Log::error("  Credentials email failed for {$developer->email}: {$e->getMessage()}");
                }
            }

            // Only send email if developer has an email address
            if (! empty($developer->email)) {
                $message = "Hello {$developer->name}\n\n";
                $message .= "Congratulations! Your developer profile has been approved.\n\n";
                $message .= "Best Regards\n";
                $message .= 'Hasan Tahseen an Admin in '.config('app.url').' platform';

                $developer->notify(new MailtrapNotification(
                    subject: 'Developer Profile Approved',
                    message: $message,
                    category: 'Developer Approval'
                ));
            }
        }

        // When youtube_url is updated, set status to PENDING for re-review
        if ($developer->wasChanged('youtube_url') && $developer->status !== DeveloperStatus::PENDING) {
            Developer::withoutEvents(fn () => $developer->update(['status' => DeveloperStatus::PENDING]));
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
