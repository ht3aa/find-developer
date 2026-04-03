<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ApplicationStatus;
use App\Enums\Currency;
use App\Enums\JobStatus;
use App\Enums\WorldGovernorate;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyJobRequest;
use App\Http\Requests\UpdateCompanyJobRequest;
use App\Jobs\AddRemoteWorkCollaboratorJob;
use App\Models\CompanyJob;
use App\Models\CompanyJobApplication;
use App\Models\JobTitle;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RemoteWorkController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', CompanyJob::class);

        $jobs = CompanyJob::query()
            ->where('user_id', auth()->id())
            ->with(['jobTitle:id,name,slug'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Dashboard/RemoteWork/Index', [
            'jobs' => $jobs,
        ]);
    }

    public function create(): Response|RedirectResponse
    {
        $this->authorize('create', CompanyJob::class);

        $pendingDraft = CompanyJob::query()
            ->where('user_id', auth()->id())
            ->where('status', JobStatus::PENDING)
            ->latest()
            ->first();

        if ($pendingDraft !== null && auth()->user()->can('update', $pendingDraft)) {
            return redirect()->route('dashboard.remote-work.edit', $pendingDraft);
        }

        return Inertia::render('Dashboard/RemoteWork/Create', [
            'jobTitles' => JobTitle::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']),
            ...$this->sharedJobFormOptions(),
        ]);
    }

    public function store(StoreCompanyJobRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['status'] = JobStatus::PENDING;

        CompanyJob::query()->create($data);

        return redirect()->route('dashboard.remote-work.index')
            ->with('success', 'Remote work post created and is pending review.');
    }

    public function edit(CompanyJob $job): Response
    {
        $this->authorize('update', $job);

        $job->load('jobTitle');

        return Inertia::render('Dashboard/RemoteWork/Edit', [
            'job' => [
                'id' => $job->id,
                'title' => $job->title,
                'slug' => $job->slug,
                'description' => $job->description,
                'company_name' => $job->company_name,
                'email' => $job->email,
                'contact_link' => $job->contact_link,
                'location' => $job->location?->value,
                'job_title_id' => $job->job_title_id,
                'salary_from' => $job->salary_from,
                'salary_to' => $job->salary_to,
                'salary_currency' => $job->salary_currency?->value,
                'requirements' => $job->requirements,
                'status' => $job->status->value,
            ],
            'jobTitles' => JobTitle::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']),
            ...$this->sharedJobFormOptions(),
        ]);
    }

    public function update(UpdateCompanyJobRequest $request, CompanyJob $job): RedirectResponse
    {
        $job->update($request->validated());

        return redirect()->route('dashboard.remote-work.index')
            ->with('success', 'Post updated.');
    }

    public function applications(CompanyJob $job): Response
    {
        $this->authorize('manageApplications', $job);

        $applications = CompanyJobApplication::query()
            ->where('company_job_id', $job->id)
            ->with(['developer' => fn ($q) => $q->with(['jobTitle:id,name', 'user:id,name,email'])])
            ->latest()
            ->paginate(20);

        return Inertia::render('Dashboard/RemoteWork/Applications', [
            'job' => [
                'id' => $job->id,
                'title' => $job->title,
                'slug' => $job->slug,
                'status' => $job->status->value,
            ],
            'applications' => $applications,
        ]);
    }

    public function accept(CompanyJobApplication $application): RedirectResponse
    {
        $this->authorize('accept', $application);

        abort_unless($application->status === ApplicationStatus::PENDING, 422);

        $application->update(['status' => ApplicationStatus::ACCEPTED]);

        AddRemoteWorkCollaboratorJob::dispatch($application->id);

        return redirect()->back()->with('success', 'Developer accepted and will be added to the Gitea repository.');
    }

    public function reject(CompanyJobApplication $application): RedirectResponse
    {
        $this->authorize('reject', $application);

        abort_unless($application->status === ApplicationStatus::PENDING, 422);

        $application->update(['status' => ApplicationStatus::REJECTED]);

        return redirect()->back()->with('success', 'Application rejected.');
    }

    /**
     * @return array{currencies: list<array{value: string, label: string}>, locations: list<array{value: string, label: string}>}
     */
    protected function sharedJobFormOptions(): array
    {
        return [
            'currencies' => collect(Currency::cases())->map(fn (Currency $c): array => [
                'value' => $c->value,
                'label' => $c->getLabel(),
            ])->values()->all(),
            'locations' => collect(WorldGovernorate::cases())->map(fn (WorldGovernorate $g): array => [
                'value' => $g->value,
                'label' => $g->getLabel(),
            ])->values()->all(),
        ];
    }
}
