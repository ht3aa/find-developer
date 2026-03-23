<?php

namespace App\Http\Requests\Dashboard;

use App\Models\DeveloperProject;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeveloperProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', DeveloperProject::class);
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
