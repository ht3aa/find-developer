<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use App\Enums\WorldGovernorate;
use App\Models\CompanyJob;
use App\Models\JobTitle;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', CompanyJob::class);
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('job_title_id') === '' || $this->input('job_title_id') === null) {
            $this->merge(['job_title_id' => null]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:company_jobs,slug'],
            'description' => ['required', 'string'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'contact_link' => ['nullable', 'url', 'max:2048'],
            'location' => ['nullable', Rule::enum(WorldGovernorate::class)],
            'job_title_id' => ['nullable', 'integer', Rule::exists(JobTitle::class, 'id')],
            'salary_from' => ['nullable', 'integer', 'min:0'],
            'salary_to' => ['nullable', 'integer', 'min:0'],
            'salary_currency' => ['required', Rule::enum(Currency::class)],
            'requirements' => ['nullable', 'string'],
        ];
    }
}
