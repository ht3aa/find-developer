<?php

namespace App\Policies;

use App\Models\HackathonSubscriber;
use App\Models\User;

class HackathonSubscriberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:HackathonSubscribers') || $user->isSuperAdmin();
    }

    public function view(User $user, HackathonSubscriber $hackathonSubscriber): bool
    {
        return $user->can('View:HackathonSubscribers') || $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->can('Create:HackathonSubscribers') || $user->isSuperAdmin();
    }

    public function update(User $user, HackathonSubscriber $hackathonSubscriber): bool
    {
        return $user->can('Update:HackathonSubscribers') || $user->isSuperAdmin();
    }

    public function delete(User $user, HackathonSubscriber $hackathonSubscriber): bool
    {
        return $user->can('Delete:HackathonSubscribers') || $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:HackathonSubscribers') || $user->isSuperAdmin();
    }

    public function restore(User $user, HackathonSubscriber $hackathonSubscriber): bool
    {
        return $user->can('Restore:HackathonSubscribers') || $user->isSuperAdmin();
    }

    public function forceDelete(User $user, HackathonSubscriber $hackathonSubscriber): bool
    {
        return $user->can('ForceDelete:HackathonSubscribers') || $user->isSuperAdmin();
    }
}
