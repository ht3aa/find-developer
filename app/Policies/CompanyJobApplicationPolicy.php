<?php

namespace App\Policies;

use App\Models\CompanyJobApplication;
use App\Models\User;

class CompanyJobApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CompanyJobApplication $application): bool
    {
        return $this->ownerOfJobOrSuperAdmin($user, $application);
    }

    /**
     * Developer applies to a job (controller also checks CompanyJobPolicy::apply).
     */
    public function create(User $user): bool
    {
        return $user->isDeveloper();
    }

    public function update(User $user, CompanyJobApplication $application): bool
    {
        return $this->ownerOfJobOrSuperAdmin($user, $application);
    }

    public function accept(User $user, CompanyJobApplication $application): bool
    {
        return $this->ownerOfJobOrSuperAdmin($user, $application);
    }

    public function reject(User $user, CompanyJobApplication $application): bool
    {
        return $this->ownerOfJobOrSuperAdmin($user, $application);
    }

    protected function ownerOfJobOrSuperAdmin(User $user, CompanyJobApplication $application): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $job = $application->companyJob;

        return $job !== null && $job->user_id === $user->id;
    }
}
