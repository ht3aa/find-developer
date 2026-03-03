<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HackathonTeamIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', \App\Models\HackathonTeam::class);
    }

    public function rules(): array
    {
        return [];
    }
}
