<?php

namespace App\Actions;

use App\Enums\DeveloperStatus;
use App\Models\Developer;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class SuspendActivityCauserAction
{
    /**
     * @throws ValidationException
     */
    public function __invoke(string $causerType, int $causerId): void
    {
        $developer = null;

        if ($causerType === User::class) {
            $user = User::find($causerId);
            if ($user?->developer) {
                $developer = $user->developer;
            }
        } elseif ($causerType === Developer::class) {
            $developer = Developer::withoutGlobalScopes()->find($causerId);
        }

        if (! $developer) {
            throw ValidationException::withMessages([
                'causer' => 'No developer profile found for this causer.',
            ]);
        }

        $developer->update(['status' => DeveloperStatus::SUSPENDED]);
    }
}
