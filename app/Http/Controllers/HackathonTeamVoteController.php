<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dashboard\VoteHackathonTeamRequest;
use App\Models\Hackathon;
use App\Models\HackathonTeam;
use App\Models\HackathonTeamVote;
use Illuminate\Http\RedirectResponse;

class HackathonTeamVoteController extends Controller
{
    public function store(VoteHackathonTeamRequest $request, Hackathon $hackathon, HackathonTeam $team): RedirectResponse
    {
        if ($team->hackathon_id !== $hackathon->id) {
            abort(404);
        }

        $developer = $request->user()->developer;

        if (! $developer) {
            abort(403);
        }

        $vote = HackathonTeamVote::firstOrNew([
            'hackathon_team_id' => $team->id,
            'subscriber_developer_id' => $developer->id,
        ]);

        $vote->is_voted = ! $vote->is_voted;
        $vote->save();

        return redirect()->route('hackathons.teams.public', $hackathon)->with('success', 'Vote updated.');
    }
}
