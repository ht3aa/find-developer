<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreDeveloperProjectRequest;
use App\Http\Requests\Dashboard\UpdateDeveloperProjectRequest;
use App\Models\DeveloperProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeveloperProjectController extends Controller
{
    /**
     * Display the developer projects listing for the authenticated developer.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        $this->authorize('viewAny', DeveloperProject::class);

        $developer = $request->user()->developer;

        if (! $developer) {
            return redirect()->route('dashboard.developer-profile.index')
                ->withErrors(['developer' => 'You must have a developer profile to manage projects.']);
        }

        $projects = DeveloperProject::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (DeveloperProject $p) => [
                'id' => $p->id,
                'title' => $p->title,
                'description' => $p->description,
                'link' => $p->link,
                'show_project' => $p->show_project,
            ]);

        $user = $request->user();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'can' => [
                'updateDeveloperProject' => $user->can('update', new DeveloperProject),
                'deleteDeveloperProject' => $user->can('delete', new DeveloperProject),
            ],
        ]);
    }

    /**
     * Show the form for creating a new developer project.
     */
    public function create(Request $request): Response|RedirectResponse
    {
        $this->authorize('create', DeveloperProject::class);

        $developer = $request->user()->developer;

        if (! $developer) {
            return redirect()->route('dashboard.developer-profile.index')
                ->withErrors(['developer' => 'You must have a developer profile to add projects.']);
        }

        return Inertia::render('Projects/Create');
    }

    /**
     * Store a newly created developer project.
     */
    public function store(StoreDeveloperProjectRequest $request): RedirectResponse
    {
        $this->authorize('create', DeveloperProject::class);

        $developer = $request->user()->developer;

        $data = $request->validated();
        $data['developer_id'] = $developer->id;
        $data['show_project'] = $data['show_project'] ?? true;

        DeveloperProject::create($data);

        return redirect()
            ->route('developer-projects.index')
            ->with('success', 'Project added successfully.');
    }

    /**
     * Show the form for editing the specified developer project.
     */
    public function edit(Request $request, DeveloperProject $developer_project): Response|RedirectResponse
    {
        $this->authorize('update', $developer_project);

        $developer = $request->user()->developer;

        if (! $developer) {
            return redirect()->route('dashboard.developer-profile.index')
                ->withErrors(['developer' => 'You must have a developer profile to edit projects.']);
        }

        return Inertia::render('Projects/Edit', [
            'project' => [
                'id' => $developer_project->id,
                'title' => $developer_project->title,
                'description' => $developer_project->description,
                'link' => $developer_project->link,
                'show_project' => $developer_project->show_project,
            ],
        ]);
    }

    /**
     * Update the specified developer project.
     */
    public function update(UpdateDeveloperProjectRequest $request, DeveloperProject $developer_project): RedirectResponse
    {
        $this->authorize('update', $developer_project);

        $data = $request->validated();
        $data['show_project'] = $data['show_project'] ?? true;

        $developer_project->update($data);

        return redirect()
            ->route('developer-projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified developer project.
     */
    public function destroy(Request $request, DeveloperProject $developer_project): RedirectResponse
    {
        $this->authorize('delete', $developer_project);

        $developer_project->delete();

        return redirect()
            ->route('developer-projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
