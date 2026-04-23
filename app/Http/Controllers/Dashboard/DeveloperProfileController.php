<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\WorldGovernorate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\DeveloperProfileIndexRequest;
use App\Http\Requests\Dashboard\DownloadDeveloperCvRequest;
use App\Http\Requests\Dashboard\UpdateDeveloperProfileRequest;
use App\Http\Resources\DeveloperResource;
use App\Models\Badge;
use App\Models\Developer;
use App\Models\JobTitle;
use App\Models\Scopes\ApprovedScope;
use App\Models\Skill;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DeveloperProfileController extends Controller
{
    /**
     * Display the developer profile edit page for the authenticated user.
     */
    public function index(DeveloperProfileIndexRequest $request): Response
    {
        $developer = $request->user()->developer;

        $locations = WorldGovernorate::selectOptions();

        if (! $developer) {
            return Inertia::render('Developers/Profile', [
                'developer' => null,
                'jobTitles' => JobTitle::active()->orderBy('name')->get(['id', 'name']),
                'locations' => $locations,
            ]);
        }

        $developer = Developer::withoutGlobalScope(ApprovedScope::class)
            ->with(['jobTitle', 'skills', 'badges', 'companies' => ['jobTitle'], 'projects'])
            ->find($developer->id);

        $developer = (new DeveloperResource($developer))->resolve();

        return Inertia::render('Developers/Profile', [
            'developer' => $developer,
            'jobTitles' => JobTitle::active()->orderBy('name')->get(['id', 'name']),
            'locations' => $locations,
        ]);
    }

    /**
     * Update the authenticated user's developer profile.
     */
    public function update(UpdateDeveloperProfileRequest $request): RedirectResponse
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
        $updateCvAutomatic = isset($data['update_cv_automatic']) ? (bool) $data['update_cv_automatic'] : null;
        unset($data['skill_ids'], $data['skill_names'], $data['cv'], $data['update_cv_automatic']);

        if ($updateCvAutomatic !== null) {
            $data['update_cv_automatic'] = $updateCvAutomatic;
        }

        $data['slug'] = Str::slug($data['name']);

        $experienceUpdated = isset($data['years_of_experience'])
            && (int) $data['years_of_experience'] !== (int) $developer->years_of_experience;

        $developer->update($data);

        if ($experienceUpdated) {
            $experienceValidatedBadge = Badge::where('slug', 'experience-validated')->first();
            if ($experienceValidatedBadge) {
                $developer->badges()->detach($experienceValidatedBadge->id);
            }
        }

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
            $ids = Skill::whereIn('name', $skillNames)->pluck('id')->all();
            $developer->skills()->sync($ids);
        }

        return redirect()
            ->route('dashboard.developer-profile.index')
            ->with('success', 'Developer profile updated successfully.');
    }

    /**
     * Download the authenticated user's developer profile as a PDF CV.
     */
    public function downloadCv(DownloadDeveloperCvRequest $request)
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
