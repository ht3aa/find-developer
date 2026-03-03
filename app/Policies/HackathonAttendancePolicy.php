<?php

namespace App\Policies;

use App\Models\HackathonAttendance;
use App\Models\User;

class HackathonAttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:HackathonAttendances') || $user->isSuperAdmin();
    }

    public function view(User $user, HackathonAttendance $hackathonAttendance): bool
    {
        return $user->can('View:HackathonAttendances') || $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->can('Create:HackathonAttendances') || $user->isSuperAdmin();
    }

    public function update(User $user, HackathonAttendance $hackathonAttendance): bool
    {
        return $user->can('Update:HackathonAttendances') || $user->isSuperAdmin();
    }

    public function delete(User $user, HackathonAttendance $hackathonAttendance): bool
    {
        return $user->can('Delete:HackathonAttendances') || $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:HackathonAttendances') || $user->isSuperAdmin();
    }

    public function restore(User $user, HackathonAttendance $hackathonAttendance): bool
    {
        return $user->can('Restore:HackathonAttendances') || $user->isSuperAdmin();
    }

    public function forceDelete(User $user, HackathonAttendance $hackathonAttendance): bool
    {
        return $user->can('ForceDelete:HackathonAttendances') || $user->isSuperAdmin();
    }
}
