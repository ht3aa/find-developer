<?php

namespace App\Http\Requests\Dashboard;

use App\Models\HackathonTeam;
use Illuminate\Foundation\Http\FormRequest;

class HackathonTeamCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', HackathonTeam::class);
    }

    public function rules(): array
    {
        return [];
    }
}
