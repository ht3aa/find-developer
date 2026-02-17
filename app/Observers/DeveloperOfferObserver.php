<?php

namespace App\Observers;

use App\Enums\OfferStatus;
use App\Models\DeveloperOffer;
use App\Notifications\MailtrapNotification;
use Illuminate\Support\Facades\Notification;

class DeveloperOfferObserver
{
    /**
     * Handle the DeveloperOffer "updated" event.
     */
    public function updated(DeveloperOffer $offer): void
    {
        if ($offer->isDirty('status') && $offer->status === OfferStatus::APPROVED) {
            $offer->load(['developer', 'jobTitle']);

            $developerMessage = "Hello {$offer->developer->name},\n\n";
            $developerMessage .= "Great news! You have received an approved offer.\n\n";
            $developerMessage .= "Company: {$offer->company_name}\n";
            $developerMessage .= 'Position: '.$offer->jobTitle->name."\n";
            if ($offer->salary_range) {
                $developerMessage .= "Salary Range: {$offer->salary_range}\n";
            }
            if ($offer->work_type) {
                $developerMessage .= "Work Type: {$offer->work_type->getLabel()}\n";
            }
            $developerMessage .= "\nMessage from the sender:\n{$offer->message}\n\n";
            $developerMessage .= "Contact Email: {$offer->contact_email}\n\n";
            $developerMessage .= "Please reach out to the sender if you are interested in this opportunity.\n\n";
            $developerMessage .= "Best Regards,\n";
            $developerMessage .= 'Hasan Tahseen an Admin in '.config('app.url').' platform';

            $offer->developer->notify(new MailtrapNotification(
                subject: 'You Received an Offer!',
                message: $developerMessage,
                category: 'Developer Offer'
            ));

            $companyMessage = "Hello,\n\n";
            $companyMessage .= "Your offer to {$offer->developer->name} ({$offer->jobTitle->name}) has been approved.\n\n";
            $companyMessage .= "The developer has been notified and may reach out to you at {$offer->contact_email} if they are interested in the opportunity.\n\n";
            $companyMessage .= "Best Regards,\n";
            $companyMessage .= 'Hasan Tahseen an Admin in '.config('app.url').' platform';

            Notification::route('mail', $offer->contact_email)
                ->notify(new MailtrapNotification(
                    subject: 'Your Offer Has Been Approved',
                    message: $companyMessage,
                    category: 'Developer Offer'
                ));
        }
    }
}
