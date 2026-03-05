<?php

namespace App\Http\Controllers;

use App\Enums\OfferStatus;
use App\Http\Requests\StoreDeveloperOfferRequest;
use App\Models\DeveloperOffer;
use Illuminate\Http\RedirectResponse;

class DeveloperOfferController extends Controller
{
    /**
     * Store a newly created developer offer.
     */
    public function store(StoreDeveloperOfferRequest $request): RedirectResponse
    {
        DeveloperOffer::create([
            'developer_ids' => $request->validated('developer_ids'),
            'user_id' => $request->user()->id,
            'company_name' => $request->validated('company_name'),
            'job_title_id' => $request->validated('job_title_id'),
            'message' => $request->validated('message'),
            'salary_range' => $request->validated('salary_range'),
            'work_type' => $request->validated('work_type'),
            'contact_email' => $request->validated('contact_email'),
            'status' => OfferStatus::PENDING,
        ]);

        return redirect()->route('home')
            ->with('success', 'Your offer has been submitted and is pending approval.');
    }
}
