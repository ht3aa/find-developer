<?php

namespace App\Observers;

use App\Models\DeveloperProject;
use App\Services\DeveloperCvBuilderService;

class DeveloperProjectObserver
{
    public function updated(DeveloperProject $developerProject): void
    {
        $this->maybeRebuildDeveloperCv($developerProject);
    }

    public function created(DeveloperProject $developerProject): void
    {
        $this->maybeRebuildDeveloperCv($developerProject);
    }

    public function deleted(DeveloperProject $developerProject): void
    {
        $this->maybeRebuildDeveloperCv($developerProject);
    }

    private function maybeRebuildDeveloperCv(DeveloperProject $developerProject): void
    {
        $developer = $developerProject->developer;

        if ($developer && $developer->update_cv_automatic) {
            app(DeveloperCvBuilderService::class)->buildAndUpdate($developer);
        }
    }
}
