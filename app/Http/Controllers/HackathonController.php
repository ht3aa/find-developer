<?php

namespace App\Http\Controllers;

use App\Http\Requests\HackathonStoreRequest;
use App\Http\Requests\HackathonUpdateRequest;
use App\Models\Badge;
use App\Models\Hackathon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class HackathonController extends Controller
{
    /**
     * Display a listing of the resource (super admin only).
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Hackathon::class);

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
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Hackathon::class);

        $badges = Badge::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Hackathons/Dashboard/Create', [
            'badges' => $badges,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HackathonStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Hackathon::class);

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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hackathon $hackathon): Response
    {
        $this->authorize('update', $hackathon);

        $badges = Badge::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

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
            ],
            'badges' => $badges,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HackathonUpdateRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $this->authorize('update', $hackathon);

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Hackathon $hackathon): RedirectResponse
    {
        $this->authorize('delete', $hackathon);

        $hackathon->delete();

        return redirect()->route('hackathons.index')
            ->with('success', 'Hackathon deleted successfully.');
    }
}
