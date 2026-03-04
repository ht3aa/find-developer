<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class DeveloperProfileIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewDeveloperProfile', $this->user()->developer);
    }

    public function rules(): array
    {
        return [];
    }
}
