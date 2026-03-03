<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class HackathonTeamMemberIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', \App\Models\HackathonTeamMember::class);
    }

    public function rules(): array
    {
        return [];
    }
}
