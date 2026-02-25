<?php

namespace App\Policies;

use App\Models\JobTitle;
use App\Models\User;

class JobTitlePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:JobTitles') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JobTitle $jobTitle): bool
    {
        return $user->can('View:JobTitles') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:JobTitles') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JobTitle $jobTitle): bool
    {
        return $user->can('Update:JobTitles') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobTitle $jobTitle): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:JobTitles') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JobTitle $jobTitle): bool
    {
        return $user->can('Restore:JobTitles') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JobTitle $jobTitle): bool
    {
        return $user->can('ForceDelete:JobTitles') || $user->isSuperAdmin();
    }
}
