<?php

use App\Models\Developer;
use App\Models\DeveloperCompany;
use App\Models\JobTitle;
use App\Models\Scopes\ApprovedScope;
use App\Models\User;

test('user without developer profile is redirected when accessing work experience index', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('work-experience.index'));

    $response
        ->assertRedirect(route('dashboard.developer-profile.index'))
        ->assertSessionHasErrors('developer');
});

test('developer can view work experience index', function () {
    $developer = Developer::withoutGlobalScope(ApprovedScope::class)->factory()->create();
    $user = $developer->user;

    $response = $this->actingAs($user)->get(route('work-experience.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('WorkExperience/Index')
        ->has('workExperiences')
        ->has('jobTitles')
    );
});

test('developer can create work experience', function () {
    $developer = Developer::withoutGlobalScope(ApprovedScope::class)->factory()->create();
    $user = $developer->user;
    $jobTitle = JobTitle::query()->first()
        ?? JobTitle::create(['name' => 'Software Engineer', 'slug' => 'software-engineer', 'is_active' => true]);

    $response = $this->actingAs($user)->post(route('work-experience.store'), [
        'company_name' => 'Acme Corp',
        'job_title_id' => $jobTitle->id,
        'description' => 'Built great software.',
        'start_date' => '2020-01-01',
        'end_date' => '2023-12-31',
        'is_current' => false,
        'show_company' => true,
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('work-experience.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('developer_companies', [
        'developer_id' => $developer->id,
        'company_name' => 'Acme Corp',
        'job_title_id' => $jobTitle->id,
    ]);
});

test('developer can create work experience as promotion from previous position', function () {
    $developer = Developer::withoutGlobalScope(ApprovedScope::class)->factory()->create();
    $user = $developer->user;
    $parent = DeveloperCompany::create([
        'developer_id' => $developer->id,
        'company_name' => 'Acme Corp',
        'job_title_id' => null,
        'description' => null,
        'start_date' => '2020-01-01',
        'end_date' => '2022-12-31',
        'is_current' => false,
        'show_company' => true,
    ]);

    $response = $this->actingAs($user)->post(route('work-experience.store'), [
        'company_name' => 'Acme Corp',
        'job_title_id' => '',
        'parent_id' => $parent->id,
        'description' => 'Promoted to senior role.',
        'start_date' => '2023-01-01',
        'end_date' => '',
        'is_current' => true,
        'show_company' => true,
    ]);

    $response->assertSessionHasNoErrors()->assertRedirect(route('work-experience.index'));

    $child = DeveloperCompany::where('developer_id', $developer->id)
        ->where('parent_id', $parent->id)
        ->first();
    expect($child)->not->toBeNull()
        ->and($child->company_name)->toBe('Acme Corp')
        ->and($child->parent_id)->toBe($parent->id);
});

test('developer can create current work experience without end date', function () {
    $developer = Developer::withoutGlobalScope(ApprovedScope::class)->factory()->create();
    $user = $developer->user;

    $response = $this->actingAs($user)->post(route('work-experience.store'), [
        'company_name' => 'Current Corp',
        'job_title_id' => '',
        'description' => null,
        'start_date' => '2022-06-01',
        'end_date' => '',
        'is_current' => true,
        'show_company' => true,
    ]);

    $response->assertSessionHasNoErrors()->assertRedirect(route('work-experience.index'));

    $experience = DeveloperCompany::where('developer_id', $developer->id)->first();
    expect($experience)->not->toBeNull()
        ->and($experience->company_name)->toBe('Current Corp')
        ->and($experience->end_date)->toBeNull()
        ->and($experience->is_current)->toBeTrue();
});

test('developer can update work experience', function () {
    $developer = Developer::withoutGlobalScope(ApprovedScope::class)->factory()->create();
    $user = $developer->user;
    $experience = DeveloperCompany::create([
        'developer_id' => $developer->id,
        'company_name' => 'Old Corp',
        'job_title_id' => null,
        'description' => 'Old role',
        'start_date' => '2019-01-01',
        'end_date' => '2021-12-31',
        'is_current' => false,
        'show_company' => true,
    ]);

    $response = $this->actingAs($user)->put(route('work-experience.update', $experience), [
        'company_name' => 'New Corp',
        'job_title_id' => '',
        'description' => 'Updated role',
        'start_date' => '2019-01-01',
        'end_date' => '2022-06-30',
        'is_current' => false,
        'show_company' => false,
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('work-experience.index'))
        ->assertSessionHas('success');

    $experience->refresh();
    expect($experience->company_name)->toBe('New Corp')
        ->and($experience->description)->toBe('Updated role')
        ->and($experience->show_company)->toBeFalse();
});

test('developer can delete work experience', function () {
    $developer = Developer::withoutGlobalScope(ApprovedScope::class)->factory()->create();
    $user = $developer->user;
    $experience = DeveloperCompany::create([
        'developer_id' => $developer->id,
        'company_name' => 'To Delete Corp',
        'job_title_id' => null,
        'description' => null,
        'start_date' => '2020-01-01',
        'end_date' => null,
        'is_current' => true,
        'show_company' => true,
    ]);

    $response = $this->actingAs($user)->delete(route('work-experience.destroy', $experience));

    $response
        ->assertRedirect(route('work-experience.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('developer_companies', ['id' => $experience->id]);
});

test('developer cannot access another developers work experience', function () {
    $developer1 = Developer::withoutGlobalScope(ApprovedScope::class)->factory()->create();
    $developer2 = Developer::withoutGlobalScope(ApprovedScope::class)->factory()->create();
    $experience = DeveloperCompany::create([
        'developer_id' => $developer2->id,
        'company_name' => 'Other Corp',
        'job_title_id' => null,
        'description' => null,
        'start_date' => '2020-01-01',
        'end_date' => null,
        'is_current' => true,
        'show_company' => true,
    ]);

    $response = $this->actingAs($developer1->user)
        ->get(route('work-experience.edit', $experience));

    $response->assertNotFound();
});
