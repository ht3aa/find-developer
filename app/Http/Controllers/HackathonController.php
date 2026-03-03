<?php

namespace App\Http\Controllers;

use App\Http\Requests\HackathonCreateRequest;
use App\Http\Requests\HackathonDestroyRequest;
use App\Http\Requests\HackathonEditRequest;
use App\Http\Requests\HackathonIndexRequest;
use App\Http\Requests\HackathonStoreRequest;
use App\Http\Requests\HackathonUpdateRequest;
use App\Models\Badge;
use App\Models\Hackathon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class HackathonController extends Controller
{
    public function index(HackathonIndexRequest $request): Response
    {
        $hackathons = Hackathon::query()
            ->with('rewardBadge:id,name')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Hackathon $h) => [
                'id' => $h->id,
                'title' => $h->title,
                'slug' => $h->slug,
                'body' => $h->body,
                'image' => $h->image,
                'image_url' => $h->image_url,
                'youtube_url' => $h->youtube_url,
                'reward_badge_id' => $h->reward_badge_id,
                'reward_badge' => $h->rewardBadge ? ['id' => $h->rewardBadge->id, 'name' => $h->rewardBadge->name] : null,
                'reward_description' => $h->reward_description,
                'start_date' => $h->start_date?->toDateString(),
                'end_date' => $h->end_date?->toDateString(),
                'created_at' => $h->created_at?->toIso8601String(),
            ]);

        $user = $request->user();

        return Inertia::render('Hackathons/Dashboard/Index', [
            'hackathons' => $hackathons,
            'can' => [
                'updateHackathon' => $user->can('update', new Hackathon),
                'deleteHackathon' => $user->can('delete', new Hackathon),
                'viewHackathonSubscribers' => $user->isSuperAdmin(),
                'viewHackathonTeams' => $user->isSuperAdmin(),
            ],
        ]);
    }

    public function create(HackathonCreateRequest $request): Response
    {
        $badges = Badge::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Hackathons/Dashboard/Create', [
            'badges' => $badges,
        ]);
    }

    public function store(HackathonStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);

        if (Hackathon::where('slug', $data['slug'])->exists()) {
            throw ValidationException::withMessages([
                'title' => 'A hackathon with this title already exists.',
            ]);
        }

        Hackathon::create($data);

        return redirect()->route('hackathons.index')
            ->with('success', 'Hackathon created successfully.');
    }

    public function edit(HackathonEditRequest $request, Hackathon $hackathon): Response
    {
        $badges = Badge::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $teams = $hackathon->teams()->orderBy('title')->get(['id', 'title']);

        return Inertia::render('Hackathons/Dashboard/Edit', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
                'slug' => $hackathon->slug,
                'body' => $hackathon->body,
                'image' => $hackathon->image,
                'image_url' => $hackathon->image_url,
                'youtube_url' => $hackathon->youtube_url,
                'reward_badge_id' => $hackathon->reward_badge_id,
                'reward_description' => $hackathon->reward_description,
                'start_date' => $hackathon->start_date?->toDateString(),
                'end_date' => $hackathon->end_date?->toDateString(),
                'current_team_id_to_vote' => $hackathon->current_team_id_to_vote,
                'enable_voting' => $hackathon->enable_voting,
            ],
            'badges' => $badges,
            'teams' => $teams,
        ]);
    }

    public function update(HackathonUpdateRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);

        if (Hackathon::where('slug', $data['slug'])->where('id', '!=', $hackathon->id)->exists()) {
            throw ValidationException::withMessages([
                'title' => 'A hackathon with this title already exists.',
            ]);
        }

        $hackathon->update($data);

        return redirect()->route('hackathons.index')
            ->with('success', 'Hackathon updated successfully.');
    }

    public function destroy(HackathonDestroyRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $hackathon->delete();

        return redirect()->route('hackathons.index')
            ->with('success', 'Hackathon deleted successfully.');
    }
}
