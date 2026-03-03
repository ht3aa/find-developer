<?php

namespace App\Http\Controllers;

use App\Enums\HackathonMemberPosition;
use App\Http\Requests\StoreHackathonTeamMemberRequest;
use App\Http\Requests\UpdateHackathonTeamMemberRequest;
use App\Models\Developer;
use App\Models\Hackathon;
use App\Models\HackathonTeam;
use App\Models\HackathonTeamMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HackathonTeamMemberController extends Controller
{
    public function index(Request $request, Hackathon $hackathon, HackathonTeam $team): Response
    {
        if ($team->hackathon_id !== $hackathon->id) {
            abort(404);
        }

        $members = $team->members()
            ->with('developer:id,name,slug,email')
            ->orderByRaw("position = 'team_leader' DESC")
            ->orderBy('created_at')
            ->get()
            ->map(fn (HackathonTeamMember $m) => [
                'id' => $m->id,
                'developer_id' => $m->developer_id,
                'developer' => $m->developer ? [
                    'id' => $m->developer->id,
                    'name' => $m->developer->name,
                    'slug' => $m->developer->slug,
                    'email' => $m->developer->email,
                ] : null,
                'position' => $m->position->value,
                'position_label' => $m->position->label(),
            ]);

        return Inertia::render('Hackathons/Dashboard/Teams/Members/Index', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
            ],
            'team' => [
                'id' => $team->id,
                'title' => $team->title,
            ],
            'members' => $members,
        ]);
    }

    public function create(Request $request, Hackathon $hackathon, HackathonTeam $team): Response
    {
        if ($team->hackathon_id !== $hackathon->id) {
            abort(404);
        }

        $memberDeveloperIds = $team->members()->pluck('developer_id');
        $developers = Developer::query()
            ->whereNotIn('id', $memberDeveloperIds)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($d) => ['id' => $d->id, 'name' => $d->name]);

        $positionOptions = array_map(
            fn (HackathonMemberPosition $p) => ['value' => $p->value, 'label' => $p->label()],
            HackathonMemberPosition::cases()
        );

        return Inertia::render('Hackathons/Dashboard/Teams/Members/Create', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
            ],
            'team' => [
                'id' => $team->id,
                'title' => $team->title,
            ],
            'developers' => $developers,
            'positionOptions' => $positionOptions,
            'storeUrl' => route('teams.members.store', [$hackathon, $team]),
        ]);
    }

    public function store(StoreHackathonTeamMemberRequest $request, Hackathon $hackathon, HackathonTeam $team): RedirectResponse
    {
        if ($team->hackathon_id !== $hackathon->id) {
            abort(404);
        }

        $data = $request->validated();
        $data['position'] = HackathonMemberPosition::from($data['position']);
        $team->members()->create($data);

        return redirect()->route('teams.members.index', [$hackathon, $team])
            ->with('success', 'Team member added successfully.');
    }

    public function edit(Request $request, Hackathon $hackathon, HackathonTeam $team, HackathonTeamMember $member): Response
    {
        if ($team->hackathon_id !== $hackathon->id || $member->hackathon_team_id !== $team->id) {
            abort(404);
        }

        $member->load('developer:id,name,slug,email');

        $positionOptions = array_map(
            fn (HackathonMemberPosition $p) => ['value' => $p->value, 'label' => $p->label()],
            HackathonMemberPosition::cases()
        );

        $memberDeveloperIds = $team->members()->where('id', '!=', $member->id)->pluck('developer_id');
        $developers = Developer::query()
            ->whereNotIn('id', $memberDeveloperIds)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($d) => ['id' => $d->id, 'name' => $d->name]);

        return Inertia::render('Hackathons/Dashboard/Teams/Members/Edit', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
            ],
            'team' => [
                'id' => $team->id,
                'title' => $team->title,
            ],
            'member' => [
                'id' => $member->id,
                'developer_id' => $member->developer_id,
                'developer' => $member->developer ? [
                    'id' => $member->developer->id,
                    'name' => $member->developer->name,
                    'slug' => $member->developer->slug,
                    'email' => $member->developer->email,
                ] : null,
                'position' => $member->position->value,
            ],
            'developers' => $developers,
            'positionOptions' => $positionOptions,
            'updateUrl' => route('teams.members.update', [$hackathon, $team, $member]),
        ]);
    }

    public function update(UpdateHackathonTeamMemberRequest $request, Hackathon $hackathon, HackathonTeam $team, HackathonTeamMember $member): RedirectResponse
    {
        if ($member->hackathon_team_id !== $team->id || $team->hackathon_id !== $hackathon->id) {
            abort(404);
        }

        $data = $request->validated();
        $member->update([
            'developer_id' => $data['developer_id'],
            'position' => HackathonMemberPosition::from($data['position']),
        ]);

        return redirect()->route('teams.members.index', [$hackathon, $team])
            ->with('success', 'Team member updated successfully.');
    }

    public function destroy(Request $request, Hackathon $hackathon, HackathonTeam $team, HackathonTeamMember $member): RedirectResponse
    {
        if ($member->hackathon_team_id !== $team->id || $team->hackathon_id !== $hackathon->id) {
            abort(404);
        }

        $member->delete();

        return redirect()->route('teams.members.index', [$hackathon, $team])
            ->with('success', 'Team member removed successfully.');
    }
}
