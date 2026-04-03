<?php

namespace App\Http\Requests;

use App\Models\CompanyJob;
use App\Models\CompanyJobApplication;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var CompanyJob $job */
        $job = $this->route('companyJob');

        return $this->user()->can('apply', $job)
            && $this->user()->can('create', CompanyJobApplication::class);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cover_message' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
