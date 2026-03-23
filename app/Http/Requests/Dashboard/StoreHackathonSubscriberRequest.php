<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\HackathonSubscriberStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHackathonSubscriberRequest extends FormRequest
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
        $hackathonId = $this->route('hackathon')->id;

        return [
            'developer_id' => [
                'required',
                'integer',
                'exists:developers,id',
                Rule::unique('hackathon_subscribers', 'developer_id')
                    ->where('hackathon_id', $hackathonId),
            ],
            'message' => ['required', 'string', 'max:2000'],
            'status' => ['required', 'string', Rule::enum(HackathonSubscriberStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'developer_id.unique' => 'This developer is already subscribed to this hackathon.',
        ];
    }
}
