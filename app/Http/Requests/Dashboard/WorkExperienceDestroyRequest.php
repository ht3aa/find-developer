<?php

namespace App\Http\Requests\Dashboard;

use App\Models\DeveloperCompany;
use Illuminate\Foundation\Http\FormRequest;

class WorkExperienceDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $workExperience = $this->route('work_experience');

        if (! $workExperience instanceof DeveloperCompany) {
            return false;
        }

        return $this->user()->can('delete', $workExperience);
    }

    public function rules(): array
    {
        return [];
    }
}
