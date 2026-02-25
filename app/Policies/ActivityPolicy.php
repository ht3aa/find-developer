<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Activitylog\Contracts\Activity;

class ActivityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {

        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Activity $activity): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Activity $activity): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Activity $activity): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Activity $activity): bool
    {
        return $user->isSuperAdmin();
        // return $user->can('restore_activity::log');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->isSuperAdmin();
        // return $user->can('restore_any_activity::log');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Activity $activity): bool
    {
        return $user->isSuperAdmin();
        // return $user->can('replicate_activity::log');
    }

    /**
     * Determine whether the user can reorder the model.
     */
    public function reorder(User $user): bool
    {
        return $user->isSuperAdmin();
        // return $user->can('reorder_activity::log');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Activity $activity): bool
    {
        return $user->isSuperAdmin();
        // return $user->can('force_delete_activity::log');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
        // return $user->can('force_delete_any_activity::log');
    }

    public function activate(User $user, Activity $activity): bool
    {
        return $user->isSuperAdmin();
        // return $user->can('activate_activity::log');
    }

    public function deactivate(User $user, Activity $activity): bool
    {
        return $user->isSuperAdmin();
        // return $user->can('deactivate_activity::log');
    }

    public function activateAny(User $user): bool
    {
        return $user->isSuperAdmin();
        // return $user->can('activate_any_activity::log');
    }

    public function deactivateAny(User $user): bool
    {
        return $user->isSuperAdmin();
        // return $user->can('deactivate_any_activity::log');
    }
}
