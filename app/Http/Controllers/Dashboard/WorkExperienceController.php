<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreWorkExperienceRequest;
use App\Http\Requests\Dashboard\UpdateWorkExperienceRequest;
use App\Models\DeveloperCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkExperienceController extends Controller
{
    /**
     * Display the work experience listing for the authenticated developer.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        $this->authorize('viewAny', DeveloperCompany::class);

        $developer = $request->user()->developer;

        if (! $developer) {
            return redirect()->route('dashboard.developer-profile.index')
                ->withErrors(['developer' => 'You must have a developer profile to manage work experience.']);
        }

        $experiences = DeveloperCompany::with(['jobTitle', 'parent'])
            ->orderByDesc('start_date')
            ->get()
            ->map(fn (DeveloperCompany $e) => [
                'id' => $e->id,
                'company_name' => $e->company_name,
                'job_title' => $e->jobTitle?->name ?? null,
                'parent_id' => $e->parent_id,
                'parent' => $e->parent ? [
                    'id' => $e->parent->id,
                    'company_name' => $e->parent->company_name,
                    'job_title' => $e->parent->jobTitle?->name ?? null,
                ] : null,
                'description' => $e->description,
                'start_date' => $e->start_date->format('Y-m-d'),
                'end_date' => $e->end_date?->format('Y-m-d'),
                'is_current' => $e->is_current,
                'show_company' => $e->show_company,
            ]);

        return Inertia::render('WorkExperience/Index', [
            'workExperiences' => $experiences,
            'jobTitles' => \App\Models\JobTitle::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Show the form for creating a new work experience.
     */
    public function create(Request $request): Response|RedirectResponse
    {
        $this->authorize('create', DeveloperCompany::class);

        $developer = $request->user()->developer;

        if (! $developer) {
            return redirect()->route('dashboard.developer-profile.index')
                ->withErrors(['developer' => 'You must have a developer profile to add work experience.']);
        }

        $parentOptions = DeveloperCompany::whereNull('parent_id')
            ->with('jobTitle')
            ->orderByDesc('start_date')
            ->get()
            ->map(fn (DeveloperCompany $e) => [
                'id' => $e->id,
                'label' => $e->company_name.' — '.($e->jobTitle?->name ?? 'N/A').' ('.$e->start_date->format('Y').')',
            ]);

        return Inertia::render('WorkExperience/Create', [
            'jobTitles' => \App\Models\JobTitle::active()->orderBy('name')->get(['id', 'name']),
            'parentOptions' => $parentOptions,
        ]);
    }

    /**
     * Store a newly created work experience.
     */
    public function store(StoreWorkExperienceRequest $request): RedirectResponse
    {
        $this->authorize('create', DeveloperCompany::class);

        $developer = $request->user()->developer;

        $data = $request->validated();
        $data['developer_id'] = $developer->id;
        $data['is_current'] = $data['is_current'] ?? false;
        $data['show_company'] = $data['show_company'] ?? true;
        $data['parent_id'] = $data['parent_id'] ?? null;

        if ($data['is_current']) {
            $data['end_date'] = null;
        }

        DeveloperCompany::create($data);

        return redirect()
            ->route('work-experience.index')
            ->with('success', 'Work experience added successfully.');
    }

    /**
     * Show the form for editing the specified work experience.
     */
    public function edit(Request $request, DeveloperCompany $workExperience): Response|RedirectResponse
    {
        $this->authorize('update', $workExperience);

        $developer = $request->user()->developer;

        if (! $developer) {
            return redirect()->route('dashboard.developer-profile.index')
                ->withErrors(['developer' => 'You must have a developer profile to edit work experience.']);
        }

        $workExperience->load(['jobTitle', 'parent']);

        $parentOptions = DeveloperCompany::whereNull('parent_id')
            ->where('id', '!=', $workExperience->id)
            ->whereNotIn('id', $workExperience->children()->pluck('id'))
            ->with('jobTitle')
            ->orderByDesc('start_date')
            ->get()
            ->map(fn (DeveloperCompany $e) => [
                'id' => $e->id,
                'label' => $e->company_name.' — '.($e->jobTitle?->name ?? 'N/A').' ('.$e->start_date->format('Y').')',
            ]);

        return Inertia::render('WorkExperience/Edit', [
            'workExperience' => [
                'id' => $workExperience->id,
                'company_name' => $workExperience->company_name,
                'job_title_id' => $workExperience->job_title_id,
                'job_title' => $workExperience->jobTitle?->name ?? null,
                'parent_id' => $workExperience->parent_id,
                'parent' => $workExperience->parent ? [
                    'id' => $workExperience->parent->id,
                    'company_name' => $workExperience->parent->company_name,
                    'job_title' => $workExperience->parent->jobTitle?->name ?? null,
                ] : null,
                'description' => $workExperience->description,
                'start_date' => $workExperience->start_date->format('Y-m-d'),
                'end_date' => $workExperience->end_date?->format('Y-m-d'),
                'is_current' => $workExperience->is_current,
                'show_company' => $workExperience->show_company,
            ],
            'jobTitles' => \App\Models\JobTitle::active()->orderBy('name')->get(['id', 'name']),
            'parentOptions' => $parentOptions,
        ]);
    }

    /**
     * Update the specified work experience.
     */
    public function update(UpdateWorkExperienceRequest $request, DeveloperCompany $workExperience): RedirectResponse
    {
        $this->authorize('update', $workExperience);

        $data = $request->validated();
        $data['is_current'] = $data['is_current'] ?? false;
        $data['show_company'] = $data['show_company'] ?? true;
        $data['parent_id'] = $data['parent_id'] ?? null;

        if ($data['is_current']) {
            $data['end_date'] = null;
        }

        $workExperience->update($data);

        return redirect()
            ->route('work-experience.index')
            ->with('success', 'Work experience updated successfully.');
    }

    /**
     * Remove the specified work experience.
     */
    public function destroy(Request $request, DeveloperCompany $workExperience): RedirectResponse
    {
        $this->authorize('delete', $workExperience);

        $workExperience->delete();

        return redirect()
            ->route('work-experience.index')
            ->with('success', 'Work experience deleted successfully.');
    }
}
