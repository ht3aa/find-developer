<?php

namespace App\Http\Requests;

use App\Models\HackathonTeam;
use Illuminate\Foundation\Http\FormRequest;

class HackathonTeamEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        $team = $this->route('team');

        if (! $team instanceof HackathonTeam) {
            return false;
        }

        return $this->user()->can('update', $team);
    }

    public function rules(): array
    {
        return [];
    }
}
