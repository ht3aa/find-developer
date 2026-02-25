<?php

namespace App\Policies;

use App\Models\Developer;
use App\Models\User;

class DeveloperPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Developers') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Developer $developer): bool
    {
        return $user->can('View:Developers') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:Developers') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Developer $developer): bool
    {
        return $user->can('Update:Developers') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Developer $developer): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Developers') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Developer $developer): bool
    {
        return $user->can('Restore:Developers') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Developer $developer): bool
    {
        return $user->can('ForceDelete:Developers') || $user->isSuperAdmin();
    }

    public function viewDeveloperProfile(User $user, ?Developer $developer = null): bool
    {
        return $user->can('View:DeveloperProfile') || $user->isSuperAdmin();
    }
    public function updateDeveloperProfile(User $user, ?Developer $developer = null): bool
    {
        return $user->can('Update:DeveloperProfile') || $user->isSuperAdmin();
    }

    public function recommend(User $user, Developer $developer): bool
    {
        return $user->can('Recommend:Developers') || $user->isSuperAdmin();
    }
}
