<?php

use App\Models\Developer;
use App\Models\DeveloperCompany;
use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Support\Facades\Config;

test('super admin can view the filament work experience index', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get('/admin/developer-work-experience')
        ->assertSuccessful();
});

test('super admin can view the filament edit work experience page', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $developer = Developer::factory()->create();
    $jobTitle = JobTitle::query()->first()
        ?? JobTitle::create([
            'name' => 'Software Engineer '.uniqid(),
            'slug' => 'software-engineer-'.uniqid(),
            'is_active' => true,
        ]);

    $experience = DeveloperCompany::create([
        'developer_id' => $developer->id,
        'company_name' => 'Acme Corp',
        'job_title_id' => $jobTitle->id,
        'description' => null,
        'start_date' => '2020-01-01',
        'end_date' => '2023-12-31',
        'is_current' => false,
        'show_company' => true,
    ]);

    $this->actingAs($admin)
        ->get('/admin/developer-work-experience/'.$experience->id.'/edit')
        ->assertSuccessful();
});

test('super admin can view the filament create work experience page', function () {
    Config::set('app.super_admin_emails', 'admin@test.com');
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get('/admin/developer-work-experience/create')
        ->assertSuccessful();
});
