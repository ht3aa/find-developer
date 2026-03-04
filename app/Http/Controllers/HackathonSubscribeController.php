<?php

namespace App\Http\Controllers;

use App\Enums\HackathonSubscriberStatus;
use App\Http\Requests\Dashboard\SubscribeHackathonRequest;
use App\Models\Hackathon;
use App\Models\HackathonSubscriber;
use Illuminate\Http\RedirectResponse;

class HackathonSubscribeController extends Controller
{
    /**
     * Subscribe the current user's developer to the hackathon with a confirmation message.
     */
    public function __invoke(SubscribeHackathonRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $developer = $request->user()->developer;

        if (! $developer) {
            return redirect()->route('hackathons.show', $hackathon->slug)
                ->with('error', 'You must have a developer profile to subscribe.');
        }

        $exists = HackathonSubscriber::query()
            ->where('hackathon_id', $hackathon->id)
            ->where('developer_id', $developer->id)
            ->exists();

        if ($exists) {
            return redirect()->route('hackathons.show', $hackathon->slug)
                ->with('info', 'You are already registered for this hackathon.');
        }

        HackathonSubscriber::create([
            'hackathon_id' => $hackathon->id,
            'developer_id' => $developer->id,
            'message' => $request->validated('message'),
            'status' => HackathonSubscriberStatus::Pending,
        ]);

        return redirect()->route('hackathons.show', $hackathon->slug)
            ->with('success', 'You have successfully registered for this hackathon.');
    }
}
