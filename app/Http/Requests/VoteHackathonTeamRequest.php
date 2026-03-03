<?php

namespace App\Http\Requests;

use App\Models\HackathonTeam;
use Illuminate\Foundation\Http\FormRequest;

class VoteHackathonTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        $team = $this->route('team');

        if (! $team instanceof HackathonTeam) {
            return false;
        }

        return $this->user()->can('vote', $team);
    }

    public function rules(): array
    {
        return [];
    }
}
