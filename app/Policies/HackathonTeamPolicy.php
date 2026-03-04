<?php

namespace App\Policies;

use App\Models\HackathonTeam;
use App\Models\User;

class HackathonTeamPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:HackathonTeams') || $user->isSuperAdmin();
    }

    public function view(User $user, HackathonTeam $hackathonTeam): bool
    {
        return $user->can('View:HackathonTeams') || $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->can('Create:HackathonTeams') || $user->isSuperAdmin();
    }

    public function update(User $user, HackathonTeam $hackathonTeam): bool
    {
        return $user->can('Update:HackathonTeams') || $user->isSuperAdmin();
    }

    public function delete(User $user, HackathonTeam $hackathonTeam): bool
    {
        return $user->can('Delete:HackathonTeams') || $user->isSuperAdmin();
    }

    public function restore(User $user, HackathonTeam $hackathonTeam): bool
    {
        return $user->can('Restore:HackathonTeams') || $user->isSuperAdmin();
    }

    public function forceDelete(User $user, HackathonTeam $hackathonTeam): bool
    {
        return $user->can('ForceDelete:HackathonTeams') || $user->isSuperAdmin();
    }

    public function vote(User $user, HackathonTeam $team): bool
    {
        if (! $user->can('Vote:HackathonTeams') && ! $user->isSuperAdmin()) {
            return false;
        }

        $developer = $user->developer;

        if (! $developer) {
            return false;
        }

        $hackathon = $team->hackathon;

        if (! $hackathon || ! $hackathon->enable_voting) {
            return false;
        }

        return $hackathon->subscribers()
            ->where('developer_id', $developer->id)
            ->exists();
    }
}
