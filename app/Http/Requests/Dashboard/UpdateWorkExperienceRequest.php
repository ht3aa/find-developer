<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkExperienceRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->input('job_title_id') === '') {
            $merge['job_title_id'] = null;
        }
        if ($this->input('parent_id') === '') {
            $merge['parent_id'] = null;
        }
        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->developer !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $developerId = $this->user()->developer->id;
        /** @var \App\Models\DeveloperCompany $workExperience */
        $workExperience = $this->route('work_experience');
        $excludedIds = [$workExperience->id, ...$workExperience->children()->pluck('id')];

        return [
            'company_name' => ['required', 'string', 'max:255'],
            'job_title_id' => ['nullable', 'integer', 'exists:job_titles,id'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('developer_companies', 'id')->where('developer_id', $developerId)->whereNull('parent_id'),
                Rule::notIn($excludedIds),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_current' => ['boolean'],
            'show_company' => ['boolean'],
        ];
    }
}
