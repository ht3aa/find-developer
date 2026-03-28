<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Badge;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BadgeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $badge = $this->route('badge');

        if (! $badge instanceof Badge) {
            return false;
        }

        return $this->user()->can('update', $badge);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $badge = $this->route('badge');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('badges', 'name')->ignore($badge),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'how_to_earn_description' => ['nullable', 'string', 'max:5000'],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ];
    }
}
