<?php

namespace App\Http\Requests\Dashboard;

use App\Models\HackathonTeamMember;
use Illuminate\Foundation\Http\FormRequest;

class HackathonTeamMemberIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', HackathonTeamMember::class);
    }

    public function rules(): array
    {
        return [];
    }
}
