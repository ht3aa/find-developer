<?php

namespace App\Http\Requests\Dashboard;

use App\Models\HackathonTeamMember;
use Illuminate\Foundation\Http\FormRequest;

class HackathonTeamMemberDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $member = $this->route('member');

        if (! $member instanceof HackathonTeamMember) {
            return false;
        }

        return $this->user()->can('delete', $member);
    }

    public function rules(): array
    {
        return [];
    }
}
