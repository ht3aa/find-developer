<?php

namespace App\Policies;

use App\Enums\BlogStatus;
use App\Models\DeveloperBlog;
use App\Models\User;

class DeveloperBlogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:DeveloperBlog') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DeveloperBlog $developerBlog): bool
    {
        return $user->can('View:DeveloperBlog') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:DeveloperBlog') || $user->isSuperAdmin() || $user->isDeveloper();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeveloperBlog $developerBlog): bool
    {
        // Super admins can always update
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Developers can only update their own blogs
        if ($user->isDeveloper() && $user->developer && $developerBlog->developer_id === $user->developer->id) {
            return true;
        }

        return $user->can('Update:DeveloperBlog');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeveloperBlog $developerBlog): bool
    {
        // Only super admins can delete
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:DeveloperBlog') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DeveloperBlog $developerBlog): bool
    {
        return $user->can('Restore:DeveloperBlog') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DeveloperBlog $developerBlog): bool
    {
        return $user->can('ForceDelete:DeveloperBlog') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can change the blog status.
     */
    public function changeStatus(User $user, DeveloperBlog $developerBlog): bool
    {
        // Only super admins can change status
        return $user->isSuperAdmin();
    }
}
