<?php

namespace App\Http\Requests\Dashboard;

use App\Models\DeveloperProject;
use Illuminate\Foundation\Http\FormRequest;

class DeveloperProjectEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        $developerProject = $this->route('developer_project');

        if (! $developerProject instanceof DeveloperProject) {
            return false;
        }

        return $this->user()->can('update', $developerProject);
    }

    public function rules(): array
    {
        return [];
    }
}
