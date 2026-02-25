<?php

use App\Models\Developer;
use App\Models\Scopes\ApprovedScope;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

test('developer can upload cv as pdf', function () {
    $developer = Developer::withoutGlobalScope(ApprovedScope::class)
        ->factory()
        ->create();
    $user = $developer->user;

    $file = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');

    $response = $this
        ->actingAs($user)
        ->put(route('dashboard.developer-profile.update'), [
            'name' => $developer->name,
            'email' => $developer->email,
            'phone' => $developer->phone,
            'job_title_id' => $developer->job_title_id,
            'years_of_experience' => $developer->years_of_experience,
            'bio' => $developer->bio,
            'portfolio_url' => $developer->portfolio_url,
            'github_url' => $developer->github_url,
            'linkedin_url' => $developer->linkedin_url,
            'youtube_url' => $developer->youtube_url,
            'is_available' => $developer->is_available,
            'availability_type' => [],
            'skill_names' => [],
            'cv' => $file,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard.developer-profile.index'));

    $developer->refresh();

    expect($developer->cv_path)->not->toBeNull();
    Storage::disk('s3')->assertExists($developer->cv_path);
});

test('cv upload rejects non pdf files', function () {
    $developer = Developer::withoutGlobalScope(ApprovedScope::class)
        ->factory()
        ->create();
    $user = $developer->user;

    $file = UploadedFile::fake()->create('document.txt', 10, 'text/plain');

    $response = $this
        ->actingAs($user)
        ->put(route('dashboard.developer-profile.update'), [
            'name' => $developer->name,
            'email' => $developer->email,
            'phone' => $developer->phone,
            'job_title_id' => $developer->job_title_id,
            'years_of_experience' => $developer->years_of_experience,
            'bio' => $developer->bio,
            'portfolio_url' => $developer->portfolio_url,
            'github_url' => $developer->github_url,
            'linkedin_url' => $developer->linkedin_url,
            'youtube_url' => $developer->youtube_url,
            'is_available' => $developer->is_available,
            'availability_type' => [],
            'skill_names' => [],
            'cv' => $file,
        ]);

    $response->assertSessionHasErrors('cv');
});

test('cv upload rejects files larger than 10mb', function () {
    $developer = Developer::withoutGlobalScope(ApprovedScope::class)
        ->factory()
        ->create();
    $user = $developer->user;

    $file = UploadedFile::fake()->create('cv.pdf', 10241, 'application/pdf');

    $response = $this
        ->actingAs($user)
        ->put(route('dashboard.developer-profile.update'), [
            'name' => $developer->name,
            'email' => $developer->email,
            'phone' => $developer->phone,
            'job_title_id' => $developer->job_title_id,
            'years_of_experience' => $developer->years_of_experience,
            'bio' => $developer->bio,
            'portfolio_url' => $developer->portfolio_url,
            'github_url' => $developer->github_url,
            'linkedin_url' => $developer->linkedin_url,
            'youtube_url' => $developer->youtube_url,
            'is_available' => $developer->is_available,
            'availability_type' => [],
            'skill_names' => [],
            'cv' => $file,
        ]);

    $response->assertSessionHasErrors('cv');
});
