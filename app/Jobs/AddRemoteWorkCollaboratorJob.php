<?php

namespace App\Jobs;

use App\Models\CompanyJobApplication;
use App\Services\CompanyJobGiteaProvisioner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AddRemoteWorkCollaboratorJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $companyJobApplicationId,
    ) {}

    public function handle(CompanyJobGiteaProvisioner $provisioner): void
    {
        $application = CompanyJobApplication::query()->findOrFail($this->companyJobApplicationId);
        $provisioner->addAcceptedApplication($application);
    }
}
