<?php

namespace App\Policies;

use App\Models\HackathonTeam;
use App\Models\User;

class HackathonTeamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, HackathonTeam $hackathonTeam): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HackathonTeam $hackathonTeam): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, HackathonTeam $hackathonTeam): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, HackathonTeam $hackathonTeam): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, HackathonTeam $hackathonTeam): bool
    {
        return false;
    }

    /**
     * Determine whether the user can vote for a team.
     */
    public function vote(User $user, HackathonTeam $team): bool
    {
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
