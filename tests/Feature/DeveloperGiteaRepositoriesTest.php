<?php

use App\Enums\ApplicationStatus;
use App\Enums\UserType;
use App\Models\CompanyJob;
use App\Models\CompanyJobApplication;
use App\Models\Developer;
use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function makeJobTitleForGitea(): JobTitle
{
    return JobTitle::query()->create([
        'name' => 'Test Role '.Str::random(6),
        'slug' => 'test-role-'.Str::random(8),
        'is_active' => true,
    ]);
}

it('forbids non-developer users from the gitea repositories dashboard', function () {
    $user = User::factory()->create([
        'user_type' => UserType::ADMIN,
    ]);
    $this->actingAs($user);

    $this->get(route('dashboard.gitea-repositories.index'))->assertForbidden();
});

it('lists accepted remote work repos with gitea metadata for developers', function () {
    config(['services.gitea.url' => 'https://git.example.com']);

    $jobTitle = makeJobTitleForGitea();
    $owner = User::factory()->create();
    $job = CompanyJob::factory()->approved()->create([
        'user_id' => $owner->id,
        'job_title_id' => $jobTitle->id,
        'gitea_owner' => 'client-login',
        'gitea_repo_name' => 'acme-remote',
        'gitea_provisioned_at' => now(),
    ]);

    $devUser = User::factory()->create([
        'user_type' => UserType::DEVELOPER,
    ]);
    $developer = Developer::factory()->create([
        'user_id' => $devUser->id,
        'job_title_id' => $jobTitle->id,
    ]);

    CompanyJobApplication::factory()->create([
        'company_job_id' => $job->id,
        'developer_id' => $developer->id,
        'status' => ApplicationStatus::ACCEPTED,
    ]);

    $this->actingAs($devUser);

    $this->get(route('dashboard.gitea-repositories.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard/GiteaRepositories/Index')
            ->where('giteaBaseUrlConfigured', true)
            ->has('repositories', 1)
            ->where('repositories.0.job_title', $job->title)
            ->where('repositories.0.repo_url', 'https://git.example.com/client-login/acme-remote')
        );
});

it('returns empty repositories when developer has no accepted gitea jobs', function () {
    $devUser = User::factory()->create([
        'user_type' => UserType::DEVELOPER,
    ]);
    Developer::factory()->create([
        'user_id' => $devUser->id,
        'job_title_id' => makeJobTitleForGitea()->id,
    ]);

    $this->actingAs($devUser);

    $this->get(route('dashboard.gitea-repositories.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard/GiteaRepositories/Index')
            ->has('repositories', 0)
        );
});

it('omits repo link when gitea base url is not configured', function () {
    config(['services.gitea.url' => null]);

    $jobTitle = makeJobTitleForGitea();
    $owner = User::factory()->create();
    $job = CompanyJob::factory()->approved()->create([
        'user_id' => $owner->id,
        'job_title_id' => $jobTitle->id,
        'gitea_owner' => 'client-login',
        'gitea_repo_name' => 'acme-remote',
        'gitea_provisioned_at' => now(),
    ]);

    $devUser = User::factory()->create([
        'user_type' => UserType::DEVELOPER,
    ]);
    $developer = Developer::factory()->create([
        'user_id' => $devUser->id,
        'job_title_id' => $jobTitle->id,
    ]);

    CompanyJobApplication::factory()->create([
        'company_job_id' => $job->id,
        'developer_id' => $developer->id,
        'status' => ApplicationStatus::ACCEPTED,
    ]);

    $this->actingAs($devUser);

    $this->get(route('dashboard.gitea-repositories.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('giteaBaseUrlConfigured', false)
            ->where('repositories.0.repo_url', null)
        );
});

it('does not list pending applications', function () {
    config(['services.gitea.url' => 'https://git.example.com']);

    $jobTitle = makeJobTitleForGitea();
    $owner = User::factory()->create();
    $job = CompanyJob::factory()->approved()->create([
        'user_id' => $owner->id,
        'job_title_id' => $jobTitle->id,
        'gitea_owner' => 'owner',
        'gitea_repo_name' => 'repo',
        'gitea_provisioned_at' => now(),
    ]);

    $devUser = User::factory()->create([
        'user_type' => UserType::DEVELOPER,
    ]);
    $developer = Developer::factory()->create([
        'user_id' => $devUser->id,
        'job_title_id' => $jobTitle->id,
    ]);

    CompanyJobApplication::factory()->create([
        'company_job_id' => $job->id,
        'developer_id' => $developer->id,
        'status' => ApplicationStatus::PENDING,
    ]);

    $this->actingAs($devUser);

    $this->get(route('dashboard.gitea-repositories.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('repositories', 0));
});
