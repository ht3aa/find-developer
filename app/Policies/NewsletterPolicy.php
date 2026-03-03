<?php

namespace App\Policies;

use App\Models\Newsletter;
use App\Models\User;

class NewsletterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Newsletters') || $user->isSuperAdmin();
    }

    public function view(User $user, Newsletter $newsletter): bool
    {
        return $user->can('View:Newsletters') || $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Newsletters') || $user->isSuperAdmin();
    }

    public function update(User $user, Newsletter $newsletter): bool
    {
        return $user->can('Update:Newsletters') || $user->isSuperAdmin();
    }

    public function delete(User $user, Newsletter $newsletter): bool
    {
        return $user->can('Delete:Newsletters') || $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Newsletters') || $user->isSuperAdmin();
    }

    public function restore(User $user, Newsletter $newsletter): bool
    {
        return $user->can('Restore:Newsletters') || $user->isSuperAdmin();
    }

    public function forceDelete(User $user, Newsletter $newsletter): bool
    {
        return $user->can('ForceDelete:Newsletters') || $user->isSuperAdmin();
    }
}
