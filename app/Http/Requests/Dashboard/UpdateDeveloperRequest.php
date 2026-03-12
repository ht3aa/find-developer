<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\AvailabilityType;
use App\Enums\Currency;
use App\Enums\WorldGovernorate;
use App\Models\Developer;
use App\Models\Scopes\ApprovedScope;
use App\Rules\UniqueDeveloperSlug;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeveloperRequest extends FormRequest
{
    public function authorize(): bool
    {
        $developer = Developer::withoutGlobalScope(ApprovedScope::class)
            ->find($this->route('developer'));

        if (! $developer instanceof Developer) {
            return false;
        }

        return $this->user()->can('update', $developer);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $routeDeveloper = $this->route('developer');
        $developerModel = $routeDeveloper instanceof Developer
            ? $routeDeveloper
            : Developer::withoutGlobalScope(ApprovedScope::class)->find($routeDeveloper);
        $developerId = $developerModel instanceof Developer ? $developerModel->id : null;
        $statusRejected = $this->input('status') === \App\Enums\DeveloperStatus::REJECTED->value;
        $statusChangedToRejected = $statusRejected
            && $developerModel instanceof Developer
            && $developerModel->status !== \App\Enums\DeveloperStatus::REJECTED;

        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                new UniqueDeveloperSlug($developerId),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('developers', 'email')->ignore($developerId),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'job_title_id' => ['nullable', 'integer', 'exists:job_titles,id'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:100'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'portfolio_url' => ['nullable', 'url', 'max:500'],
            'github_url' => ['nullable', 'url', 'max:500'],
            'linkedin_url' => ['nullable', 'url', 'max:500'],
            'youtube_url' => ['nullable', 'string', 'max:500'],
            'location' => ['nullable', Rule::enum(WorldGovernorate::class)],
            'expected_salary_from' => ['nullable', 'integer', 'min:0'],
            'expected_salary_to' => ['nullable', 'integer', 'min:0', 'gte:expected_salary_from'],
            'salary_currency' => ['nullable', Rule::enum(Currency::class)],
            'is_available' => ['boolean'],
            'availability_type' => ['nullable', 'array'],
            'availability_type.*' => [Rule::enum(AvailabilityType::class)],
            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
            'skill_names' => ['nullable', 'array'],
            'skill_names.*' => ['string', 'max:255'],
            'badge_names' => ['nullable', 'array'],
            'badge_names.*' => ['string', 'max:255'],
            'status' => ['nullable', Rule::enum(\App\Enums\DeveloperStatus::class)],
            'rejection_reason' => [
                $statusChangedToRejected ? 'required' : 'nullable',
                'string',
                'max:2000',
            ],
            'recommended_by_us' => ['boolean'],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
