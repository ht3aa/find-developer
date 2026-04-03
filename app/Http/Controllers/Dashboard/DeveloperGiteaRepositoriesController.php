<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\CompanyJobApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeveloperGiteaRepositoriesController extends Controller
{
    /**
     * Remote work posts where the developer was accepted and the project has a Gitea repository.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user !== null && $user->isDeveloper(), 403);

        $developer = $user->developer;
        abort_unless($developer !== null, 403);

        $repositories = CompanyJobApplication::query()
            ->where('developer_id', $developer->id)
            ->where('status', ApplicationStatus::ACCEPTED)
            ->whereHas('companyJob', fn ($q) => $q
                ->whereNotNull('gitea_owner')
                ->whereNotNull('gitea_repo_name'))
            ->with(['companyJob:id,title,slug,gitea_owner,gitea_repo_name,gitea_provisioned_at'])
            ->latest()
            ->get()
            ->map(fn (CompanyJobApplication $app): array => [
                'application_id' => $app->id,
                'job_title' => $app->companyJob->title,
                'job_slug' => $app->companyJob->slug,
                'repo_url' => $app->companyJob->gitea_repository_url,
                'gitea_owner' => $app->companyJob->gitea_owner,
                'gitea_repo_name' => $app->companyJob->gitea_repo_name,
                'provisioned_at' => $app->companyJob->gitea_provisioned_at?->toIso8601String(),
            ]);

        return Inertia::render('Dashboard/GiteaRepositories/Index', [
            'repositories' => $repositories,
            'giteaBaseUrlConfigured' => filled(config('services.gitea.url')),
        ]);
    }
}
