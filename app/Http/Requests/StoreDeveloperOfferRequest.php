<?php

namespace App\Http\Requests;

use App\Enums\AvailabilityType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDeveloperOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'developer_ids' => ['required', 'array', 'min:1'],
            'developer_ids.*' => ['integer', 'exists:developers,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'job_title_id' => ['required', 'integer', 'exists:job_titles,id'],
            'message' => ['required', 'string', 'max:5000'],
            'salary_range' => ['nullable', 'string', 'max:100'],
            'work_type' => ['nullable', Rule::enum(AvailabilityType::class)],
            'contact_email' => ['required', 'email'],
        ];
    }
}
