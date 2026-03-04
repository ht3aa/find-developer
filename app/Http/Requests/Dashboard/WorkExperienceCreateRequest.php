<?php

namespace App\Http\Requests\Dashboard;

use App\Models\DeveloperCompany;
use Illuminate\Foundation\Http\FormRequest;

class WorkExperienceCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', DeveloperCompany::class);
    }

    public function rules(): array
    {
        return [];
    }
}
