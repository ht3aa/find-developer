<?php

namespace App\Policies;

use App\Models\FeedPostComment;
use App\Models\User;

class FeedPostCommentPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, FeedPostComment $feedPostComment): bool
    {
        return $user->id === $feedPostComment->user_id || $user->isSuperAdmin();
    }
}
