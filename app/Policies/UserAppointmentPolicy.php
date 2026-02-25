<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAppointment;

class UserAppointmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:UserAppointment') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserAppointment $userAppointment): bool
    {
        return $user->can('View:UserAppointment') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserAppointment $userAppointment): bool
    {
        return $user->can('Update:UserAppointment') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserAppointment $userAppointment): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserAppointment $userAppointment): bool
    {
        return $user->can('Restore:UserAppointment') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserAppointment $userAppointment): bool
    {
        return $user->isSuperAdmin();
    }
}
