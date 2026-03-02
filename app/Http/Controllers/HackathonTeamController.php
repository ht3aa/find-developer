<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHackathonTeamRequest;
use App\Http\Requests\UpdateHackathonTeamRequest;
use App\Models\Hackathon;
use App\Models\HackathonTeam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class HackathonTeamController extends Controller
{
    public function index(Request $request, Hackathon $hackathon): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $teams = $hackathon->teams()
            ->orderBy('title')
            ->get()
            ->map(fn(HackathonTeam $t) => [
                'id' => $t->id,
                'title' => $t->title,
                'logo' => $t->logo,
                'logo_url' => $t->logo_url,
                'members_count' => $t->members()->count(),
            ]);

        return Inertia::render('Hackathons/Dashboard/Teams/Index', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
            ],
            'teams' => $teams,
        ]);
    }

    public function create(Request $request, Hackathon $hackathon): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        return Inertia::render('Hackathons/Dashboard/Teams/Create', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
            ],
            'storeUrl' => route('hackathons.teams.store', $hackathon),
        ]);
    }

    public function store(StoreHackathonTeamRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $data = $request->validated();
        $logoFile = $data['logo'] ?? null;
        unset($data['logo']);

        $team = $hackathon->teams()->create($data);

        if ($logoFile) {
            $path = $logoFile->store("hackathon-teams/{$team->id}", ['disk' => 's3']);
            $team->update(['logo' => $path]);
        }

        return redirect()->route('hackathons.teams.index', $hackathon)
            ->with('success', 'Team created successfully.');
    }

    public function edit(Request $request, Hackathon $hackathon, HackathonTeam $team): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        if ($team->hackathon_id !== $hackathon->id) {
            abort(404);
        }

        return Inertia::render('Hackathons/Dashboard/Teams/Edit', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
            ],
            'team' => [
                'id' => $team->id,
                'title' => $team->title,
                'logo' => $team->logo,
                'logo_url' => $team->logo_url,
            ],
            'updateUrl' => route('hackathons.teams.update', [$hackathon, $team]),
        ]);
    }

    public function update(UpdateHackathonTeamRequest $request, Hackathon $hackathon, HackathonTeam $team): RedirectResponse
    {
        if ($team->hackathon_id !== $hackathon->id) {
            abort(404);
        }

        $data = $request->validated();
        $logoFile = $data['logo'] ?? null;
        unset($data['logo']);

        $team->update($data);

        if ($logoFile) {
            if ($team->logo) {
                Storage::disk('s3')->delete($team->logo);
            }
            $path = $logoFile->store("hackathon-teams/{$team->id}", ['disk' => 's3']);
            $team->update(['logo' => $path]);
        }

        return redirect()->route('hackathons.teams.index', $hackathon)
            ->with('success', 'Team updated successfully.');
    }

    public function destroy(Request $request, Hackathon $hackathon, HackathonTeam $team): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        if ($team->hackathon_id !== $hackathon->id) {
            abort(404);
        }

        $team->delete();

        return redirect()->route('hackathons.teams.index', $hackathon)
            ->with('success', 'Team deleted successfully.');
    }
}
