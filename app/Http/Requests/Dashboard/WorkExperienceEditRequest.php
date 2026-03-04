<?php

namespace App\Http\Requests\Dashboard;

use App\Models\DeveloperCompany;
use Illuminate\Foundation\Http\FormRequest;

class WorkExperienceEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        $workExperience = $this->route('work_experience');

        if (! $workExperience instanceof DeveloperCompany) {
            return false;
        }

        return $this->user()->can('update', $workExperience);
    }

    public function rules(): array
    {
        return [];
    }
}
