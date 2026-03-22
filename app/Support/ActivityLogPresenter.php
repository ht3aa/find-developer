<?php

namespace App\Support;

use App\Enums\DeveloperStatus;
use App\Models\Developer;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class ActivityLogPresenter
{
    public static function causerEmail(Activity $activity): ?string
    {
        return isset($activity->causer->email) ? (string) $activity->causer->email : null;
    }

    public static function isCauserSuspended(Activity $activity): bool
    {
        $developer = null;
        if ($activity->causer_type === User::class && $activity->causer_id) {
            $developer = Developer::withoutGlobalScopes()
                ->where('user_id', $activity->causer_id)
                ->first();
        } elseif ($activity->causer_type === Developer::class && $activity->causer_id) {
            $developer = Developer::withoutGlobalScopes()->find($activity->causer_id);
        }

        return $developer?->status === DeveloperStatus::SUSPENDED;
    }

    public static function canSuspendCauser(Activity $activity): bool
    {
        if (! $activity->causer_type || $activity->causer_id === null) {
            return false;
        }

        if (self::isCauserSuspended($activity)) {
            return false;
        }

        return in_array($activity->causer_type, [User::class, Developer::class], true);
    }

    public static function subjectLabel(Activity $activity): ?string
    {
        $subject = $activity->subject;
        if (! $subject) {
            return null;
        }

        if (method_exists($subject, 'getActivityLogSubjectLabel')) {
            return $subject->getActivityLogSubjectLabel();
        }

        if (isset($subject->name)) {
            return (string) $subject->name;
        }

        if (isset($subject->title)) {
            return (string) $subject->title;
        }

        return (string) $activity->subject_id;
    }
}
