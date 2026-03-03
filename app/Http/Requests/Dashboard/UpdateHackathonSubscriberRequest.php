<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\HackathonSubscriberStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHackathonSubscriberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request (super admin only).
     */
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:2000'],
            'status' => ['required', 'string', Rule::enum(HackathonSubscriberStatus::class)],
        ];
    }
}
