<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\OfferStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeveloperOfferRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::enum(OfferStatus::class)],
        ];
    }
}
