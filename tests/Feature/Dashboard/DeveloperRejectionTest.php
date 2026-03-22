<?php

use App\Enums\DeveloperStatus;
use App\Filament\Resources\Developers\Pages\EditDeveloper;
use App\Models\Developer;
use App\Models\JobTitle;
use App\Models\User;
use App\Notifications\DeveloperRejectedNotification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('rejecting developer requires rejection reason and sends email', function () {
    Notification::fake();

    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $jobTitle = JobTitle::query()->first()
        ?? JobTitle::create(['name' => 'Developer', 'slug' => 'developer', 'is_active' => true]);

    $developer = Developer::factory()->create([
        'name' => 'Test Developer',
        'email' => 'developer@test.com',
        'status' => DeveloperStatus::PENDING,
        'job_title_id' => $jobTitle->id,
        'user_id' => null,
    ]);

    Livewire::actingAs($admin)
        ->test(EditDeveloper::class, ['record' => $developer->id])
        ->set('data.status', DeveloperStatus::REJECTED->value)
        ->set('data.rejection_reason', 'Profile does not meet our quality standards.')
        ->call('save')
        ->assertHasNoErrors();

    $developer->refresh();
    expect($developer->status)->toBe(DeveloperStatus::REJECTED);

    Notification::assertSentTo(
        $developer,
        DeveloperRejectedNotification::class,
        fn (DeveloperRejectedNotification $n) => $n->reason === 'Profile does not meet our quality standards.'
    );
});

test('rejecting developer without reason fails validation', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $jobTitle = JobTitle::query()->first()
        ?? JobTitle::create(['name' => 'Developer', 'slug' => 'developer', 'is_active' => true]);

    $developer = Developer::factory()->create([
        'name' => 'Test Developer',
        'email' => 'developer2@test.com',
        'status' => DeveloperStatus::PENDING,
        'job_title_id' => $jobTitle->id,
        'user_id' => null,
    ]);

    Livewire::actingAs($admin)
        ->test(EditDeveloper::class, ['record' => $developer->id])
        ->set('data.status', DeveloperStatus::REJECTED->value)
        ->set('data.rejection_reason', '')
        ->call('save')
        ->assertHasErrors(['data.rejection_reason']);
});

test('updating already rejected developer does not require rejection reason', function () {
    Notification::fake();

    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $jobTitle = JobTitle::query()->first()
        ?? JobTitle::create(['name' => 'Developer', 'slug' => 'developer', 'is_active' => true]);

    $developer = Developer::factory()->create([
        'name' => 'Test Developer',
        'email' => 'developer3@test.com',
        'status' => DeveloperStatus::REJECTED,
        'job_title_id' => $jobTitle->id,
        'user_id' => null,
    ]);

    Livewire::actingAs($admin)
        ->test(EditDeveloper::class, ['record' => $developer->id])
        ->set('data.name', 'Updated Name')
        ->set('data.status', DeveloperStatus::REJECTED->value)
        ->call('save')
        ->assertHasNoErrors();

    Notification::assertNothingSent();
});
