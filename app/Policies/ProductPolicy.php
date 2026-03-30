<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Product') || $user->isSuperAdmin();
    }

    public function view(User $user, Product $product): bool
    {
        return $user->can('View:Product') || $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Product') || $user->isSuperAdmin();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->can('Update:Product') || $user->isSuperAdmin();
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Product') || $user->isSuperAdmin();
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->can('Restore:Product') || $user->isSuperAdmin();
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->can('ForceDelete:Product') || $user->isSuperAdmin();
    }
}
