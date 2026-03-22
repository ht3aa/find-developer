<?php

namespace App\Policies;

use App\Models\MessageAttachment;
use App\Models\User;

class MessageAttachmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, MessageAttachment $messageAttachment): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, MessageAttachment $messageAttachment): bool
    {
        return false;
    }

    public function delete(User $user, MessageAttachment $messageAttachment): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
