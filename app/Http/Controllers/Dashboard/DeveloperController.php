<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UpdateDeveloperRequest;
use App\Http\Requests\StoreDeveloperRequest;
use App\Models\Developer;
use App\Models\Scopes\ApprovedScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DeveloperController extends Controller
{
    /**
     * Display a paginated listing of all developers (including pending/rejected).
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Developer::class);
        $search = $request->query('search');
        $searchTerm = is_string($search) ? trim($search) : '';

        $query = Developer::withoutGlobalScope(ApprovedScope::class)
            ->with(['jobTitle'])
            ->orderBy('name');

        if ($searchTerm !== '') {
            $term = '%'.addcslashes($searchTerm, '%_\\').'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('slug', 'like', $term);
            });
        }

        $developers = $query->paginate(15)->withQueryString()->through(fn (Developer $d) => [
            'id' => $d->id,
            'name' => $d->name,
            'slug' => $d->slug,
            'email' => $d->email,
            'job_title' => $d->jobTitle?->name ?? null,
            'years_of_experience' => $d->years_of_experience,
            'status' => $d->status->value,
            'status_label' => $d->status->getLabel(),
            'is_available' => $d->is_available,
            'profile_url' => $d->slug ? route('developers.show', $d->slug) : null,
        ]);

        return Inertia::render('Developers/Index', [
            'developers' => $developers,
            'filters' => [
                'search' => $searchTerm,
            ],
        ]);
    }

    /**
     * Show the form for creating a new developer.
     */
    public function create(): Response
    {
        $this->authorize('create', Developer::class);
        $users = \App\Models\User::whereDoesntHave('developer')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('Developers/Create', [
            'users' => $users,
            'jobTitles' => \App\Models\JobTitle::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created developer.
     */
    public function store(StoreDeveloperRequest $request): RedirectResponse
    {
        $this->authorize('create', Developer::class);
        $data = $request->validated();
        $skillIds = $data['skill_ids'] ?? null;
        $skillNames = $data['skill_names'] ?? null;
        $cvFile = $data['cv'] ?? null;
        unset($data['skill_ids'], $data['skill_names'], $data['cv']);

        $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        $data['status'] = $data['status'] ?? \App\Enums\DeveloperStatus::PENDING;
        $data['is_available'] = $data['is_available'] ?? false;
        $data['recommended_by_us'] = $data['recommended_by_us'] ?? false;

        $developer = Developer::withoutGlobalScope(ApprovedScope::class)->create($data);

        if ($skillIds !== null) {
            $developer->skills()->sync($skillIds);
        } elseif ($skillNames !== null) {
            $ids = \App\Models\Skill::whereIn('name', $skillNames)->pluck('id')->all();
            $developer->skills()->sync($ids);
        }

        if ($cvFile) {
            $disk = 's3';
            $path = $cvFile->store("cvs/developer-{$developer->id}", ['disk' => $disk]);
            $developer->update(['cv_path' => $path]);
        }

        return redirect()
            ->route('developers.index')
            ->with('success', 'Developer created successfully.');
    }

    /**
     * Show the form for editing the specified developer.
     */
    public function edit(string $developer): Response
    {
        $this->authorize('update', $developer);
        $developer = Developer::withoutGlobalScope(ApprovedScope::class)
            ->with(['jobTitle', 'skills', 'badges'])
            ->findOrFail($developer);

        $developer = (new \App\Http\Resources\DeveloperResource($developer))->resolve();

        return Inertia::render('Developers/Edit', [
            'developer' => $developer,
            'jobTitles' => \App\Models\JobTitle::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified developer.
     */
    public function update(UpdateDeveloperRequest $request, string $developer): RedirectResponse
    {
        $this->authorize('update', $developer);
        $developer = Developer::withoutGlobalScope(ApprovedScope::class)->findOrFail($developer);

        $data = $request->validated();
        $skillIds = $data['skill_ids'] ?? null;
        $skillNames = $data['skill_names'] ?? null;
        $cvFile = $data['cv'] ?? null;
        unset($data['skill_ids'], $data['skill_names'], $data['cv']);

        $developer->update($data);

        if ($skillIds !== null) {
            $developer->skills()->sync($skillIds);
        } elseif ($skillNames !== null) {
            $ids = \App\Models\Skill::whereIn('name', $skillNames)->pluck('id')->all();
            $developer->skills()->sync($ids);
        }

        if ($cvFile) {
            $disk = 's3';
            if ($developer->cv_path) {
                Storage::disk($disk)->delete($developer->cv_path);
            }
            $path = $cvFile->store("cvs/developer-{$developer->id}", ['disk' => $disk]);
            $developer->update(['cv_path' => $path]);
        }

        return redirect()
            ->route('developers.index')
            ->with('success', 'Developer updated successfully.');
    }
}
