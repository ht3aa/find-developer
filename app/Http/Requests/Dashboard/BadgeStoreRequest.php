<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Badge;
use Illuminate\Foundation\Http\FormRequest;

class BadgeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Badge::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:badges,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ];
    }
}
