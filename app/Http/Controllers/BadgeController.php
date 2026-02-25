<?php

namespace App\Http\Controllers;

use App\Http\Requests\BadgeStoreRequest;
use App\Http\Requests\BadgeUpdateRequest;
use App\Models\Badge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BadgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Badge::class);

        $badges = Badge::query()
            ->withCount('developers')
            ->orderBy('name')
            ->get();

        return Inertia::render('Badges/Index', [
            'badges' => $badges,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Badge::class);

        return Inertia::render('Badges/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BadgeStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Badge::class);
        Badge::create($request->validated());

        return redirect()->route('badges.index')
            ->with('success', 'Badge created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Badge $badge): Response
    {
        $this->authorize('update', $badge);

        return Inertia::render('Badges/Edit', [
            'badge' => $badge,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BadgeUpdateRequest $request, Badge $badge): RedirectResponse
    {
        $this->authorize('update', $badge);
        $badge->update($request->validated());

        return redirect()->route('badges.index')
            ->with('success', 'Badge updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Badge $badge): RedirectResponse
    {
        $this->authorize('delete', $badge);
        $badge->delete();

        return redirect()->route('badges.index')
            ->with('success', 'Badge deleted successfully.');
    }
}
