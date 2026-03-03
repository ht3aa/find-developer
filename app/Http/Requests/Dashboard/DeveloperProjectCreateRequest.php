<?php

namespace App\Http\Requests\Dashboard;

use App\Models\DeveloperProject;
use Illuminate\Foundation\Http\FormRequest;

class DeveloperProjectCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', DeveloperProject::class);
    }

    public function rules(): array
    {
        return [];
    }
}
