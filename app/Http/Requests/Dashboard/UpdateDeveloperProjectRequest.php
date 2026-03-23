<?php

namespace App\Http\Requests\Dashboard;

use App\Models\DeveloperProject;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDeveloperProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        $developerProject = $this->route('developer_project');

        if (! $developerProject instanceof DeveloperProject) {
            return false;
        }

        return $this->user()->can('update', $developerProject);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'link' => ['nullable', 'string', 'url', 'max:500'],
            'show_project' => ['boolean'],
        ];
    }
}
