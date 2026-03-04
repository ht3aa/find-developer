<?php

namespace App\Observers;

use App\Enums\HackathonSubscriberStatus;
use App\Models\HackathonSubscriber;
use App\Notifications\HackathonSubscriberConfirmedNotification;

class HackathonSubscriberObserver
{
    /**
     * Handle the HackathonSubscriber "updated" event.
     * Send email to the developer when status is changed to Confirmed.
     */
    public function updated(HackathonSubscriber $subscriber): void
    {
        if (! $subscriber->wasChanged('status') || $subscriber->status !== HackathonSubscriberStatus::Confirmed) {
            return;
        }

        $subscriber->load(['developer', 'hackathon']);

        if (! $subscriber->developer || ! $subscriber->hackathon) {
            return;
        }

        $subscriber->developer->notify(new HackathonSubscriberConfirmedNotification(
            subscriber: $subscriber,
            hackathon: $subscriber->hackathon
        ));
    }
}
