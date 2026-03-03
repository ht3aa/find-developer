<?php

namespace App\Http\Requests\Dashboard;

use App\Models\HackathonTeam;
use Illuminate\Foundation\Http\FormRequest;

class HackathonTeamDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $team = $this->route('team');

        if (! $team instanceof HackathonTeam) {
            return false;
        }

        return $this->user()->can('delete', $team);
    }

    public function rules(): array
    {
        return [];
    }
}
