<?php

namespace App\Policies;

use App\Models\DeveloperProject;
use App\Models\User;

class DeveloperProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:DeveloperProjects');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DeveloperProject $developerProject): bool
    {
        return $user->can('View:DeveloperProjects');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:DeveloperProjects');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeveloperProject $developerProject): bool
    {
        return $user->can('Update:DeveloperProjects');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeveloperProject $developerProject): bool
    {
        return $user->can('Delete:DeveloperProjects');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:DeveloperProjects');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DeveloperProject $developerProject): bool
    {
        return $user->can('Restore:DeveloperProjects');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DeveloperProject $developerProject): bool
    {
        return $user->can('ForceDelete:DeveloperProjects');
    }
}
