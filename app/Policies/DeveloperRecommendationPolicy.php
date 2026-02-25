<?php

namespace App\Policies;

use App\Models\DeveloperRecommendation;
use App\Models\User;

class DeveloperRecommendationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:DeveloperRecommendations') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DeveloperRecommendation $developerRecommendation): bool
    {
        return $user->can('View:DeveloperRecommendations') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:DeveloperRecommendations') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeveloperRecommendation $developerRecommendation): bool
    {
        return $user->can('Update:DeveloperRecommendations') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeveloperRecommendation $developerRecommendation): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:DeveloperRecommendations') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DeveloperRecommendation $developerRecommendation): bool
    {
        return $user->can('Restore:DeveloperRecommendations') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DeveloperRecommendation $developerRecommendation): bool
    {
        return $user->can('ForceDelete:DeveloperRecommendations') || $user->isSuperAdmin();
    }
}
