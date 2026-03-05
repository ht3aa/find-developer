<?php

namespace App\Observers;

use App\Enums\OfferStatus;
use App\Mail\CompanyOfferApprovedMail;
use App\Mail\DeveloperOfferApprovedMail;
use App\Models\DeveloperOffer;
use Illuminate\Support\Facades\Mail;

class DeveloperOfferObserver
{
    /**
     * Handle the DeveloperOffer "updated" event.
     */
    public function updated(DeveloperOffer $offer): void
    {
        if (! $offer->wasChanged('status') || $offer->status !== OfferStatus::APPROVED) {
            return;
        }

        $offer->load(['jobTitle']);

        $developers = $offer->developers();

        foreach ($developers as $developer) {
            Mail::to($developer->email)->send(new DeveloperOfferApprovedMail(
                developerName: $developer->name,
                companyName: $offer->company_name,
                jobTitleName: $offer->jobTitle?->name ?? 'N/A',
                salaryRange: $offer->salary_range,
                workType: $offer->work_type?->getLabel(),
                contactEmail: $offer->contact_email,
                senderMessage: $offer->message
            ));
        }

        $developersData = $developers->map(fn ($d) => [
            'name' => $d->name,
            'years_of_experience' => $d->years_of_experience,
            'job_title' => $d->jobTitle?->name,
            'profile_url' => $d->slug ? route('developers.show', $d->slug) : null,
        ])->toArray();

        Mail::to($offer->contact_email)->send(new CompanyOfferApprovedMail(
            developers: $developersData,
            jobTitleName: $offer->jobTitle?->name ?? 'N/A',
            contactEmail: $offer->contact_email
        ));
    }
}
