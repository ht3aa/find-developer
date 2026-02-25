<?php

namespace App\Policies;

use App\Models\DeveloperCompany;
use App\Models\User;

class DeveloperCompanyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:DeveloperCompany') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DeveloperCompany $developerCompany): bool
    {
        return $user->can('View:DeveloperCompany') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:DeveloperCompany') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeveloperCompany $developerCompany): bool
    {
        return $user->can('Update:DeveloperCompany') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeveloperCompany $developerCompany): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:DeveloperCompany') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DeveloperCompany $developerCompany): bool
    {
        return $user->can('Restore:DeveloperCompany') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DeveloperCompany $developerCompany): bool
    {
        return $user->can('ForceDelete:DeveloperCompany') || $user->isSuperAdmin();
    }
}
