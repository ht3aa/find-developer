<?php

namespace App\Policies;

use App\Models\HackathonTeamMember;
use App\Models\User;

class HackathonTeamMemberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:HackathonTeamMembers') || $user->isSuperAdmin();
    }

    public function view(User $user, HackathonTeamMember $hackathonTeamMember): bool
    {
        return $user->can('View:HackathonTeamMembers') || $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->can('Create:HackathonTeamMembers') || $user->isSuperAdmin();
    }

    public function update(User $user, HackathonTeamMember $hackathonTeamMember): bool
    {
        return $user->can('Update:HackathonTeamMembers') || $user->isSuperAdmin();
    }

    public function delete(User $user, HackathonTeamMember $hackathonTeamMember): bool
    {
        return $user->can('Delete:HackathonTeamMembers') || $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:HackathonTeamMembers') || $user->isSuperAdmin();
    }

    public function restore(User $user, HackathonTeamMember $hackathonTeamMember): bool
    {
        return $user->can('Restore:HackathonTeamMembers') || $user->isSuperAdmin();
    }

    public function forceDelete(User $user, HackathonTeamMember $hackathonTeamMember): bool
    {
        return $user->can('ForceDelete:HackathonTeamMembers') || $user->isSuperAdmin();
    }
}
