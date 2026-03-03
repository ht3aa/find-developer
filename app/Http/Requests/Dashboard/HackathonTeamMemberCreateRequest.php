<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class HackathonTeamMemberCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\HackathonTeamMember::class);
    }

    public function rules(): array
    {
        return [];
    }
}
