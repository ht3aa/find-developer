<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Hackathon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHackathonAttendanceRequest extends FormRequest
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
        /** @var Hackathon $hackathon */
        $hackathon = $this->route('hackathon');
        $subscriberDeveloperIds = $hackathon->subscribers()->pluck('developer_id')->all();

        $dateRules = ['required', 'date'];
        if ($hackathon->start_date) {
            $dateRules[] = 'after_or_equal:'.$hackathon->start_date->format('Y-m-d');
        }
        if ($hackathon->end_date) {
            $dateRules[] = 'before_or_equal:'.$hackathon->end_date->format('Y-m-d');
        }

        return [
            'developer_id' => ['required', 'integer', Rule::in($subscriberDeveloperIds)],
            'date' => $dateRules,
            'attended' => ['required', 'boolean'],
        ];
    }
}
