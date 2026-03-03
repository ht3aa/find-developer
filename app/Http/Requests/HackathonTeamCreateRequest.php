<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HackathonTeamCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\HackathonTeam::class);
    }

    public function rules(): array
    {
        return [];
    }
}
