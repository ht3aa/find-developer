<?php

namespace App\Http\Requests\Dashboard;

use App\Models\DeveloperCompany;
use Illuminate\Foundation\Http\FormRequest;

class WorkExperienceIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', DeveloperCompany::class);
    }

    public function rules(): array
    {
        return [];
    }
}
