<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\HackathonMemberPosition;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHackathonTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $team = $this->route('team');
        $member = $this->route('member');

        return [
            'developer_id' => [
                'required',
                'integer',
                'exists:developers,id',
                Rule::unique('hackathon_team_members', 'developer_id')
                    ->where('hackathon_team_id', $team->id)
                    ->ignore($member->id),
            ],
            'position' => ['required', 'string', Rule::enum(HackathonMemberPosition::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'developer_id.unique' => 'This developer is already a member of this team.',
        ];
    }
}
