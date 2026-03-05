<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Developer;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SuspendCauserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'causer_type' => ['required', 'string', Rule::in([User::class, Developer::class])],
            'causer_id' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'causer_type.required' => 'Causer type is required.',
            'causer_type.in' => 'Invalid causer type.',
            'causer_id.required' => 'Causer ID is required.',
            'causer_id.integer' => 'Causer ID must be an integer.',
        ];
    }
}
