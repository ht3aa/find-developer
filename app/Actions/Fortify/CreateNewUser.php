<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\AvailabilityType;
use App\Enums\DeveloperStatus;
use App\Enums\UserType;
use App\Models\Developer;
use App\Models\JobTitle;
use App\Models\Scopes\ApprovedScope;
use App\Models\Skill;
use App\Models\User;
use App\Rules\UniqueDeveloperSlug;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user and their developer profile.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                new UniqueDeveloperSlug(),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'job_title_id' => ['required', 'integer', 'exists:job_titles,id'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:100'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'portfolio_url' => ['nullable', 'url', 'max:500'],
            'github_url' => ['nullable', 'url', 'max:500'],
            'linkedin_url' => ['nullable', 'url', 'max:500'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:developers,email'],
            'youtube_url' => ['nullable', 'string', 'max:500'],
            'is_available' => ['boolean'],
            'availability_type' => ['nullable', 'array'],
            'availability_type.*' => [Rule::enum(AvailabilityType::class)],
            'skill_names' => ['nullable', 'array'],
            'skill_names.*' => ['string', 'max:255'],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];

        $validated = Validator::make($input, $rules)->validate();



        $jobTitleId = isset($validated['job_title_id']) && $validated['job_title_id']
            ? (int) $validated['job_title_id']
            : null;
        if ($jobTitleId !== null && ! JobTitle::where('id', $jobTitleId)->exists()) {
            $jobTitleId = null;
        }

        $developerData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'slug' => Str::slug($input['name']) . '-' . Str::random(6),
            'phone' => $validated['phone'] ?? null,
            'job_title_id' => $jobTitleId,
            'years_of_experience' => (int) ($validated['years_of_experience'] ?? 0),
            'bio' => $validated['bio'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'github_url' => $validated['github_url'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'youtube_url' => $validated['youtube_url'] ?? null,
            'is_available' => (bool) ($validated['is_available'] ?? false),
            'availability_type' => $validated['availability_type'] ?? [],
            'status' => DeveloperStatus::PENDING,
            'recommended_by_us' => false,
        ];

        $developer = Developer::withoutGlobalScope(ApprovedScope::class)->create($developerData);

        $skillNames = $validated['skill_names'] ?? [];
        if (! empty($skillNames)) {
            $ids = Skill::whereIn('name', $skillNames)->pluck('id')->all();
            $developer->skills()->sync($ids);
        }

        $cvFile = $input['cv'] ?? null;
        if ($cvFile && $cvFile instanceof \Illuminate\Http\UploadedFile) {
            $disk = 's3';
            $path = $cvFile->store("cvs/developer-{$developer->id}", ['disk' => $disk]);
            $developer->update(['cv_path' => $path]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Str::uuid(),
            'user_type' => UserType::DEVELOPER,
        ]);

        $developer->update(['user_id' => $user->id]);

        return $user;
    }
}
