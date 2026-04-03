<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Enums\JobStatus;
use App\Http\Requests\StoreCompanyJobApplicationRequest;
use App\Models\CompanyJob;
use App\Models\CompanyJobApplication;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RemoteWorkController extends Controller
{
    public function index(): Response
    {
        $jobs = CompanyJob::query()
            ->approved()
            ->with(['jobTitle:id,name,slug'])
            ->latest()
            ->paginate(12);

        return Inertia::render('RemoteWork/Index', [
            'jobs' => $jobs,
        ]);
    }

    public function show(CompanyJob $companyJob): Response
    {
        abort_unless($companyJob->status === JobStatus::APPROVED, 404);

        $this->authorize('view', $companyJob);

        $companyJob->load(['jobTitle:id,name,slug', 'user:id,name']);

        $user = request()->user();
        $developer = $user?->developer;
        $hasApplied = false;
        if ($user && $developer) {
            $hasApplied = CompanyJobApplication::query()
                ->where('company_job_id', $companyJob->id)
                ->where('developer_id', $developer->id)
                ->exists();
        }

        return Inertia::render('RemoteWork/Show', [
            'job' => [
                'id' => $companyJob->id,
                'title' => $companyJob->title,
                'slug' => $companyJob->slug,
                'description' => $companyJob->description,
                'company_name' => $companyJob->company_name,
                'requirements' => $companyJob->requirements,
                'salary_from' => $companyJob->salary_from,
                'salary_to' => $companyJob->salary_to,
                'salary_currency' => $companyJob->salary_currency?->value,
                'location' => $companyJob->location?->value,
                'location_label' => $companyJob->location?->getLabel(),
                'job_title' => $companyJob->jobTitle,
                'created_at' => $companyJob->created_at?->toIso8601String(),
            ],
            'canApply' => $user ? $user->can('apply', $companyJob) : false,
            'hasApplied' => $hasApplied,
        ]);
    }

    public function apply(StoreCompanyJobApplicationRequest $request, CompanyJob $companyJob): RedirectResponse
    {
        abort_unless($companyJob->status === JobStatus::APPROVED, 404);

        $developer = $request->user()->developer;
        if ($developer === null) {
            abort(403, 'You need a developer profile to apply.');
        }

        $exists = CompanyJobApplication::query()
            ->where('company_job_id', $companyJob->id)
            ->where('developer_id', $developer->id)
            ->exists();

        if ($exists) {
            return redirect()
                ->route('remote-work.show', $companyJob)
                ->with('error', 'You have already applied to this post.');
        }

        CompanyJobApplication::query()->create([
            'company_job_id' => $companyJob->id,
            'developer_id' => $developer->id,
            'status' => ApplicationStatus::PENDING,
            'cover_message' => $request->validated('cover_message'),
        ]);

        return redirect()
            ->route('remote-work.show', $companyJob)
            ->with('success', 'Your application was submitted.');
    }
}
