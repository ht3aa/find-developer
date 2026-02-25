<?php

namespace App\Policies;

use App\Models\ExperienceTask;
use App\Models\User;

class ExperienceTaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:ExperienceTask') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExperienceTask $experienceTask): bool
    {
        return $user->can('View:ExperienceTask') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:ExperienceTask') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExperienceTask $experienceTask): bool
    {
        return $user->can('Update:ExperienceTask') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExperienceTask $experienceTask): bool
    {
        return $user->can('Delete:ExperienceTask') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ExperienceTask') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExperienceTask $experienceTask): bool
    {
        return $user->can('Restore:ExperienceTask') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExperienceTask $experienceTask): bool
    {
        return $user->can('ForceDelete:ExperienceTask') || $user->isSuperAdmin();
    }
}
