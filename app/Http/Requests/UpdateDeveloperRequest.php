<?php

namespace App\Http\Requests;

use App\Enums\AvailabilityType;
use App\Enums\Currency;
use App\Enums\WorldGovernorate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeveloperRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $developer = $this->user()->developer;

        return $developer !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $developer = $this->user()->developer;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('developers', 'email')->ignore($developer),
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
        ];
    }
}
