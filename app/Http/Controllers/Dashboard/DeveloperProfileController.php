<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDeveloperRequest;
use App\Http\Resources\DeveloperResource;
use App\Models\Developer;
use App\Models\Scopes\ApprovedScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeveloperProfileController extends Controller
{
    /**
     * Display the developer profile edit page for the authenticated user.
     */
    public function index(Request $request): Response
    {
        $developer = $request->user()->developer;

        if (! $developer) {
            return Inertia::render('Developers/Profile', [
                'developer' => null,
                'jobTitles' => \App\Models\JobTitle::active()->orderBy('name')->get(['id', 'name']),
            ]);
        }

        $developer = Developer::withoutGlobalScope(ApprovedScope::class)
            ->with(['jobTitle', 'skills', 'badges'])
            ->find($developer->id);

        $developer = (new DeveloperResource($developer))->resolve();

        return Inertia::render('Developers/Profile', [
            'developer' => $developer,
            'jobTitles' => \App\Models\JobTitle::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the authenticated user's developer profile.
     */
    public function update(UpdateDeveloperRequest $request): RedirectResponse
    {
        $developer = auth()->user()->developer;

        if (! $developer) {
            return redirect()->route('dashboard.developer-profile.index')
                ->withErrors(['developer' => 'You do not have a developer profile.']);
        }

        $data = $request->validated();
        $skillIds = $data['skill_ids'] ?? null;
        $skillNames = $data['skill_names'] ?? null;
        unset($data['skill_ids'], $data['skill_names']);

        $developer->update($data);

        if ($skillIds !== null) {
            $developer->skills()->sync($skillIds);
        } elseif ($skillNames !== null) {
            $ids = \App\Models\Skill::whereIn('name', $skillNames)->pluck('id')->all();
            $developer->skills()->sync($ids);
        }

        return redirect()
            ->route('dashboard.developer-profile.index')
            ->with('success', 'Developer profile updated successfully.');
    }
}
