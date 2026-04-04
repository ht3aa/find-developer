<?php

use App\Enums\DeveloperStatus;
use App\Enums\JobStatus;
use App\Enums\UserType;
use App\Models\CompanyJob;
use App\Models\CompanyJobApplication;
use App\Models\Developer;
use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function makeJobTitle(): JobTitle
{
    return JobTitle::query()->create([
        'name' => 'Test Role '.Str::random(6),
        'slug' => 'test-role-'.Str::random(8),
        'is_active' => true,
    ]);
}

it('shows only approved posts on public remote work index', function () {
    $jobTitle = makeJobTitle();
    $owner = User::factory()->create();
    CompanyJob::factory()->approved()->create([
        'user_id' => $owner->id,
        'job_title_id' => $jobTitle->id,
        'title' => 'Approved gig',
    ]);
    CompanyJob::factory()->create([
        'user_id' => $owner->id,
        'job_title_id' => $jobTitle->id,
        'title' => 'Pending gig',
        'status' => JobStatus::PENDING,
    ]);

    $response = $this->get(route('remote-work.index'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('RemoteWork/Index')
        ->has('jobs.data', 1)
        ->where('jobs.data.0.title', 'Approved gig')
    );
});

it('rejects unverified user from remote work dashboard create', function () {
    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    $this->get(route('dashboard.remote-work.create'))->assertForbidden();
});

it('allows verified user to open create remote work form', function () {
    makeJobTitle();
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('dashboard.remote-work.create'))->assertOk();
});

it('redirects remote work create to edit when a pending post already exists', function () {
    $jobTitle = makeJobTitle();
    $user = User::factory()->create();
    $pending = CompanyJob::factory()->create([
        'user_id' => $user->id,
        'job_title_id' => $jobTitle->id,
        'status' => JobStatus::PENDING,
    ]);
    $this->actingAs($user);

    $this->get(route('dashboard.remote-work.create'))
        ->assertRedirect(route('dashboard.remote-work.edit', $pending));
});

it('shares createRemoteWorkPost true for verified users on remote work create', function () {
    makeJobTitle();
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('dashboard.remote-work.create'))->assertInertia(fn ($page) => $page
        ->where('auth.can.createRemoteWorkPost', true)
    );
});

it('shares createRemoteWorkPost false for unverified users', function () {
    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    $this->get(route('home'))->assertInertia(fn ($page) => $page
        ->where('auth.can.createRemoteWorkPost', false)
    );
});

it('stores a pending remote work post for the authenticated user', function () {
    $jobTitle = makeJobTitle();
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('dashboard.remote-work.store'), [
        'title' => 'Need React developer',
        'description' => 'We need help building a dashboard.',
        'company_name' => 'Acme Inc',
        'email' => 'work@acme.com',
        'job_title_id' => $jobTitle->id,
        'salary_currency' => 'IQD',
    ]);

    $response->assertRedirect(route('dashboard.remote-work.index'));
    $this->assertDatabaseHas('company_jobs', [
        'user_id' => $user->id,
        'title' => 'Need React developer',
        'status' => JobStatus::PENDING->value,
    ]);
});

it('stores a pending remote work post without job title when omitted', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('dashboard.remote-work.store'), [
        'title' => 'General help needed',
        'description' => 'We need help building a dashboard.',
        'company_name' => 'Acme Inc',
        'email' => 'work@acme.com',
        'salary_currency' => 'IQD',
    ]);

    $response->assertRedirect(route('dashboard.remote-work.index'));
    $this->assertDatabaseHas('company_jobs', [
        'user_id' => $user->id,
        'title' => 'General help needed',
        'job_title_id' => null,
        'status' => JobStatus::PENDING->value,
    ]);
});

it('shows public detail for approved posts to guests', function () {
    $jobTitle = makeJobTitle();
    $owner = User::factory()->create();
    $job = CompanyJob::factory()->approved()->create([
        'user_id' => $owner->id,
        'job_title_id' => $jobTitle->id,
        'title' => 'Public approved',
    ]);

    $this->get(route('remote-work.show', $job))->assertOk();
});

it('returns 404 for non-approved remote work detail', function () {
    $jobTitle = makeJobTitle();
    $owner = User::factory()->create();
    $job = CompanyJob::factory()->create([
        'user_id' => $owner->id,
        'job_title_id' => $jobTitle->id,
        'status' => JobStatus::PENDING,
    ]);

    $this->get(route('remote-work.show', $job))->assertNotFound();
});

it('lets a developer apply to an approved post', function () {
    $jobTitle = makeJobTitle();
    $owner = User::factory()->create();
    $job = CompanyJob::factory()->approved()->create([
        'user_id' => $owner->id,
        'job_title_id' => $jobTitle->id,
    ]);

    $devUser = User::factory()->create([
        'user_type' => UserType::DEVELOPER,
    ]);
    Developer::factory()->create([
        'user_id' => $devUser->id,
        'job_title_id' => $jobTitle->id,
    ]);

    $this->actingAs($devUser);

    $response = $this->post(route('remote-work.apply', $job), [
        'cover_message' => 'I would love to help.',
    ]);

    $response->assertRedirect(route('remote-work.show', $job));
    $this->assertDatabaseHas('company_job_applications', [
        'company_job_id' => $job->id,
        'status' => 'pending',
    ]);
});

it('includes gitea repository url on remote work dashboard index when provisioned', function () {
    config(['services.gitea.url' => 'https://git.example.com']);
    $jobTitle = makeJobTitle();
    $user = User::factory()->create();
    CompanyJob::factory()->create([
        'user_id' => $user->id,
        'job_title_id' => $jobTitle->id,
        'gitea_owner' => 'owner',
        'gitea_repo_name' => 'my-repo',
        'gitea_provisioned_at' => now(),
    ]);
    $this->actingAs($user);

    $this->get(route('dashboard.remote-work.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard/RemoteWork/Index')
            ->has('jobs.data', 1)
            ->where('jobs.data.0.gitea_repository_url', 'https://git.example.com/owner/my-repo')
        );
});

it('shows job owner the applications page with applicant developer slug and status', function () {
    $jobTitle = makeJobTitle();
    $owner = User::factory()->create();
    $job = CompanyJob::factory()->approved()->create([
        'user_id' => $owner->id,
        'job_title_id' => $jobTitle->id,
    ]);

    $devUser = User::factory()->create([
        'user_type' => UserType::DEVELOPER,
    ]);
    $developer = Developer::factory()->create([
        'user_id' => $devUser->id,
        'job_title_id' => $jobTitle->id,
        'status' => DeveloperStatus::APPROVED,
    ]);

    CompanyJobApplication::factory()->create([
        'company_job_id' => $job->id,
        'developer_id' => $developer->id,
    ]);

    $this->actingAs($owner);

    $this->get(route('dashboard.remote-work.applications', $job))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard/RemoteWork/Applications')
            ->has('applications.data', 1)
            ->where('applications.data.0.developer.slug', $developer->slug)
            ->where('applications.data.0.developer.status', DeveloperStatus::APPROVED->value)
        );
});
