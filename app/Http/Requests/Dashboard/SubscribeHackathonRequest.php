<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Developer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubscribeHackathonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only logged-in developers with viewDeveloperProfile can subscribe.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('viewDeveloperProfile', Developer::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:2000'],
        ];
    }
}
