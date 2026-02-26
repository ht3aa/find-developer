<?php

namespace App\Observers;

use App\Enums\DeveloperStatus;
use App\Models\Developer;
use App\Notifications\AdminDeveloperNotification;
use Illuminate\Support\Facades\Log;

class AdminDeveloperObserver
{
    private const ADMIN_EMAIL = 'ht3aa2001@gmail.com';

    public function created(Developer $developer): void
    {
        $this->sendAdminNotification($developer, 'New Developer Created');
    }

    public function updated(Developer $developer): void
    {
        if ($developer->isDirty('status') && $developer->status === DeveloperStatus::EXPERIENCE_CHANGED) {
            $this->sendAdminNotification($developer, 'Developer Status Changed to Experience Changed');
        }
    }

    private function sendAdminNotification(Developer $developer, string $subject): void
    {
        try {
            $admin = new \App\Models\User();
            $admin->email = self::ADMIN_EMAIL;

            $admin->notify(new AdminDeveloperNotification($developer, $subject));
        } catch (\Throwable $e) {
            Log::error("Admin notification failed for developer {$developer->id}: {$e->getMessage()}");
        }
    }
}
