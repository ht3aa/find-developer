<?php

namespace App\Policies;

use App\Models\Hackathon;
use App\Models\User;

class HackathonPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Hackathons') || $user->isSuperAdmin();
    }

    public function view(User $user, Hackathon $hackathon): bool
    {
        return $user->can('View:Hackathons') || $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Hackathons') || $user->isSuperAdmin();
    }

    public function update(User $user, Hackathon $hackathon): bool
    {
        return $user->can('Update:Hackathons') || $user->isSuperAdmin();
    }

    public function delete(User $user, Hackathon $hackathon): bool
    {
        return $user->can('Delete:Hackathons') || $user->isSuperAdmin();
    }

    public function restore(User $user, Hackathon $hackathon): bool
    {
        return $user->can('Restore:Hackathons') || $user->isSuperAdmin();
    }

    public function forceDelete(User $user, Hackathon $hackathon): bool
    {
        return $user->can('ForceDelete:Hackathons') || $user->isSuperAdmin();
    }
}
