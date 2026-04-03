<?php

namespace App\Jobs;

use App\Models\CompanyJob;
use App\Services\CompanyJobGiteaProvisioner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProvisionCompanyJobGiteaJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $companyJobId,
    ) {}

    public function handle(CompanyJobGiteaProvisioner $provisioner): void
    {
        $job = CompanyJob::query()->findOrFail($this->companyJobId);
        $provisioner->provisionRepositoryForApprovedJob($job);
    }
}
