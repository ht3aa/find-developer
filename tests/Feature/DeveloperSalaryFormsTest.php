<?php

use App\Enums\Currency;
use App\Enums\WorldGovernorate;
use App\Models\Developer;
use App\Models\JobTitle;
use App\Models\Scopes\ApprovedScope;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;

test('developer profile update persists expected salary fields', function () {
    $jobTitle = JobTitle::query()->first()
        ?? JobTitle::query()->create([
            'name' => 'Developer',
            'slug' => 'developer',
            'is_active' => true,
        ]);

    $developer = Developer::factory()->create([
        'job_title_id' => $jobTitle->id,
        'expected_salary_from' => null,
        'expected_salary_to' => null,
    ]);
    $user = $developer->user;
    $user->markEmailAsVerified();
    Permission::findOrCreate('Update:DeveloperProfile', 'web');
    $user->givePermissionTo('Update:DeveloperProfile');

    $response = $this->actingAs($user)->put(route('dashboard.developer-profile.update'), [
        'name' => $developer->name,
        'email' => $developer->email,
        'phone' => $developer->phone,
        'job_title_id' => $jobTitle->id,
        'years_of_experience' => $developer->years_of_experience,
        'bio' => $developer->bio,
        'portfolio_url' => $developer->portfolio_url,
        'github_url' => $developer->github_url,
        'linkedin_url' => $developer->linkedin_url,
        'youtube_url' => $developer->youtube_url,
        'is_available' => $developer->is_available,
        'availability_type' => [],
        'skill_names' => [],
        'expected_salary_from' => 1_000_000,
        'expected_salary_to' => 2_000_000,
        'salary_currency' => Currency::USD->value,
        'location' => WorldGovernorate::BAGHDAD->value,
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard.developer-profile.index'));

    $developer->refresh();

    expect($developer->expected_salary_from)->toBe(1_000_000)
        ->and($developer->expected_salary_to)->toBe(2_000_000)
        ->and($developer->salary_currency)->toBe(Currency::USD)
        ->and($developer->location)->toBe(WorldGovernorate::BAGHDAD);
});

test('registration stores salary on developer profile', function () {
    Notification::fake();

    $jobTitle = JobTitle::query()->first()
        ?? JobTitle::query()->create([
            'name' => 'Developer',
            'slug' => 'developer-salary-reg',
            'is_active' => true,
        ]);

    $email = 'newdev-salary-'.uniqid('', true).'@example.com';

    $this->post(route('register.store'), [
        'name' => 'Salary Reg User',
        'email' => $email,
        'job_title_id' => $jobTitle->id,
        'years_of_experience' => 3,
        'bio' => 'Bio',
        'is_available' => true,
        'availability_type' => [],
        'skill_names' => [],
        'expected_salary_from' => 500_000,
        'expected_salary_to' => 900_000,
        'salary_currency' => 'SAR',
        'location' => WorldGovernorate::BASRA->value,
    ])->assertRedirect();

    $dev = Developer::withoutGlobalScope(ApprovedScope::class)->where('email', $email)->first();

    expect($dev)->not->toBeNull()
        ->and($dev->expected_salary_from)->toBe(500_000)
        ->and($dev->expected_salary_to)->toBe(900_000)
        ->and($dev->salary_currency)->toBe(Currency::SAR)
        ->and($dev->location)->toBe(WorldGovernorate::BASRA);
});
