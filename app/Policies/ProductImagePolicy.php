<?php

namespace App\Policies;

use App\Models\ProductImage;
use App\Models\User;

class ProductImagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:ProductImage') || $user->isSuperAdmin();
    }

    public function view(User $user, ProductImage $productImage): bool
    {
        return $user->can('View:ProductImage') || $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->can('Create:ProductImage') || $user->isSuperAdmin();
    }

    public function update(User $user, ProductImage $productImage): bool
    {
        return $user->can('Update:ProductImage') || $user->isSuperAdmin();
    }

    public function delete(User $user, ProductImage $productImage): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ProductImage') || $user->isSuperAdmin();
    }

    public function restore(User $user, ProductImage $productImage): bool
    {
        return $user->can('Restore:ProductImage') || $user->isSuperAdmin();
    }

    public function forceDelete(User $user, ProductImage $productImage): bool
    {
        return $user->can('ForceDelete:ProductImage') || $user->isSuperAdmin();
    }
}
