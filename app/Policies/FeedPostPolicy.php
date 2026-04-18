<?php

namespace App\Policies;

use App\Models\FeedPost;
use App\Models\User;

class FeedPostPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, FeedPost $feedPost): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, FeedPost $feedPost): bool
    {
        return $user->id === $feedPost->user_id || $user->isSuperAdmin();
    }

    public function delete(User $user, FeedPost $feedPost): bool
    {
        return $user->id === $feedPost->user_id || $user->isSuperAdmin();
    }
}
