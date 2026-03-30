<?php

namespace App\Policies;

use App\Models\ProductPrice;
use App\Models\User;

class ProductPricePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:ProductPrice') || $user->isSuperAdmin();
    }

    public function view(User $user, ProductPrice $productPrice): bool
    {
        return $user->can('View:ProductPrice') || $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->can('Create:ProductPrice') || $user->isSuperAdmin();
    }

    public function update(User $user, ProductPrice $productPrice): bool
    {
        return $user->can('Update:ProductPrice') || $user->isSuperAdmin();
    }

    public function delete(User $user, ProductPrice $productPrice): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ProductPrice') || $user->isSuperAdmin();
    }

    public function restore(User $user, ProductPrice $productPrice): bool
    {
        return $user->can('Restore:ProductPrice') || $user->isSuperAdmin();
    }

    public function forceDelete(User $user, ProductPrice $productPrice): bool
    {
        return $user->can('ForceDelete:ProductPrice') || $user->isSuperAdmin();
    }
}
