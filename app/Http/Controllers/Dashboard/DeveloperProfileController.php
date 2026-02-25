<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDeveloperRequest;
use App\Http\Resources\DeveloperResource;
use App\Models\Developer;
use App\Models\Scopes\ApprovedScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $cvFile = $data['cv'] ?? null;
        unset($data['skill_ids'], $data['skill_names'], $data['cv']);

        $developer->update($data);

        if ($cvFile) {
            $disk = 's3';
            if ($developer->cv_path) {
                Storage::disk($disk)->delete($developer->cv_path);
            }
            $path = $cvFile->store("cvs/developer-{$developer->id}", ['disk' => $disk]);
            $developer->update(['cv_path' => $path]);
        }

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

    /**
     * Download the authenticated user's developer profile as a PDF CV.
     */
    public function downloadCv(Request $request)
    {
        $developer = $request->user()->developer;

        if (! $developer) {
            abort(404, 'You do not have a developer profile.');
        }

        $developer = Developer::withoutGlobalScope(ApprovedScope::class)
            ->with([
                'jobTitle',
                'skills',
                'companies' => ['jobTitle'],
                'projects',
            ])
            ->findOrFail($developer->id);

        $filename = str($developer->name)->slug()->append('-cv.pdf')->toString();

        $pdf = Pdf::loadView('developer-cv', ['developer' => $developer])
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}
