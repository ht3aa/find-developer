<?php

namespace App\Policies;

use App\Models\HackathonTeamVote;
use App\Models\User;

class HackathonTeamVotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:HackathonTeamVotes') || $user->isSuperAdmin();
    }

    public function view(User $user, HackathonTeamVote $hackathonTeamVote): bool
    {
        return $user->can('View:HackathonTeamVotes') || $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->can('Create:HackathonTeamVotes') || $user->isSuperAdmin();
    }

    public function update(User $user, HackathonTeamVote $hackathonTeamVote): bool
    {
        return $user->can('Update:HackathonTeamVotes') || $user->isSuperAdmin();
    }

    public function delete(User $user, HackathonTeamVote $hackathonTeamVote): bool
    {
        return $user->can('Delete:HackathonTeamVotes') || $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:HackathonTeamVotes') || $user->isSuperAdmin();
    }

    public function restore(User $user, HackathonTeamVote $hackathonTeamVote): bool
    {
        return $user->can('Restore:HackathonTeamVotes') || $user->isSuperAdmin();
    }

    public function forceDelete(User $user, HackathonTeamVote $hackathonTeamVote): bool
    {
        return $user->can('ForceDelete:HackathonTeamVotes') || $user->isSuperAdmin();
    }
}
