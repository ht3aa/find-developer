<?php

namespace App\Http\Requests\Dashboard;

use App\Models\DeveloperProject;
use Illuminate\Foundation\Http\FormRequest;

class DeveloperProjectDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $developerProject = $this->route('developer_project');

        if (! $developerProject instanceof DeveloperProject) {
            return false;
        }

        return $this->user()->can('delete', $developerProject);
    }

    public function rules(): array
    {
        return [];
    }
}
