<?php

namespace App\Policies;

use App\Enums\JobStatus;
use App\Models\CompanyJob;
use App\Models\User;

class CompanyJobPolicy
{
    /**
     * Dashboard listing: verified users (own posts) or staff permissions.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin()
            || $user->can('ViewAny:Jobs')
            || $user->hasVerifiedEmail();
    }

    /**
     * Public detail: approved posts are visible to everyone; otherwise owner or staff.
     */
    public function view(?User $user, CompanyJob $job): bool
    {
        if ($job->status === JobStatus::APPROVED) {
            return true;
        }

        if ($user === null) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->can('View:Jobs')) {
            return true;
        }

        return $job->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CompanyJob $job): bool
    {
        if ($user->isSuperAdmin() || $user->can('Update:Jobs')) {
            return true;
        }

        return $job->user_id === $user->id && $job->status === JobStatus::PENDING;
    }

    /**
     * Apply to an approved post as a developer (not the owner).
     */
    public function apply(User $user, CompanyJob $job): bool
    {
        if ($job->status !== JobStatus::APPROVED) {
            return false;
        }

        if ($job->user_id === $user->id) {
            return false;
        }

        return $user->isDeveloper();
    }

    /**
     * Review applications for a post (owner or super admin).
     */
    public function manageApplications(User $user, CompanyJob $job): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $job->user_id === $user->id;
    }

    public function delete(User $user, CompanyJob $job): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $job->user_id === $user->id && $job->status === JobStatus::PENDING;
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Jobs') || $user->isSuperAdmin();
    }

    public function restore(User $user, CompanyJob $job): bool
    {
        return $user->can('Restore:Jobs') || $user->isSuperAdmin();
    }

    public function forceDelete(User $user, CompanyJob $job): bool
    {
        return $user->can('ForceDelete:Jobs') || $user->isSuperAdmin();
    }
}
