<?php

namespace App\Observers;

use App\Models\DeveloperCompany;
use App\Services\DeveloperCvBuilderService;

class DeveloperCompanyObserver
{
    public function updated(DeveloperCompany $developerCompany): void
    {
        $this->maybeRebuildDeveloperCv($developerCompany);
    }

    public function created(DeveloperCompany $developerCompany): void
    {
        $this->maybeRebuildDeveloperCv($developerCompany);
    }

    public function deleted(DeveloperCompany $developerCompany): void
    {
        $this->maybeRebuildDeveloperCv($developerCompany);
    }

    private function maybeRebuildDeveloperCv(DeveloperCompany $developerCompany): void
    {
        $developer = $developerCompany->developer;

        if ($developer && $developer->update_cv_automatic) {
            app(DeveloperCvBuilderService::class)->buildAndUpdate($developer);
        }
    }
}
