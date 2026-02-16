<?php

namespace App\Policies;

use App\Models\DeveloperOffer;
use App\Models\User;

class DeveloperOfferPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:DeveloperOffers') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DeveloperOffer $developerOffer): bool
    {
        return $user->can('View:DeveloperOffers') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:DeveloperOffers') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeveloperOffer $developerOffer): bool
    {
        return $user->can('Update:DeveloperOffers') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeveloperOffer $developerOffer): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:DeveloperOffers') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DeveloperOffer $developerOffer): bool
    {
        return $user->can('Restore:DeveloperOffers') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DeveloperOffer $developerOffer): bool
    {
        return $user->can('ForceDelete:DeveloperOffers') || $user->isSuperAdmin();
    }
}
