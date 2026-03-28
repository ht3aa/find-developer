<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Category') || $user->isSuperAdmin();
    }

    public function view(User $user, Category $category): bool
    {
        return $user->can('View:Category') || $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Category') || $user->isSuperAdmin();
    }

    public function update(User $user, Category $category): bool
    {
        return $user->can('Update:Category') || $user->isSuperAdmin();
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Category') || $user->isSuperAdmin();
    }

    public function restore(User $user, Category $category): bool
    {
        return $user->can('Restore:Category') || $user->isSuperAdmin();
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return $user->can('ForceDelete:Category') || $user->isSuperAdmin();
    }
}
