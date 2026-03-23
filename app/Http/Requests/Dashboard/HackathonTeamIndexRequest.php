<?php

namespace App\Http\Requests\Dashboard;

use App\Models\HackathonTeam;
use Illuminate\Foundation\Http\FormRequest;

class HackathonTeamIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', HackathonTeam::class);
    }

    public function rules(): array
    {
        return [];
    }
}
