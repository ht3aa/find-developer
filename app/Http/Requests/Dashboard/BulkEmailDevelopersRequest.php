<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Developer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BulkEmailDevelopersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Developer::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'developer_ids' => ['required', 'array', 'min:1'],
            'developer_ids.*' => ['integer', 'exists:developers,id'],
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:100'],
        ];
    }
}
