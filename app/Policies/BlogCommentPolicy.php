<?php

namespace App\Policies;

use App\Models\BlogComment;
use App\Models\User;

class BlogCommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:BlogComment') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BlogComment $blogComment): bool
    {
        return $user->can('View:BlogComment') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:BlogComment') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BlogComment $blogComment): bool
    {
        return $user->can('Update:BlogComment') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BlogComment $blogComment): bool
    {
        return $user->can('Delete:BlogComment') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:BlogComment') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BlogComment $blogComment): bool
    {
        return $user->can('Restore:BlogComment') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BlogComment $blogComment): bool
    {
        return $user->can('ForceDelete:BlogComment') || $user->isSuperAdmin();
    }
}
