<?php

namespace App\Http\Controllers;

use App\Enums\HackathonSubscriberStatus;
use App\Http\Requests\HackathonSubscriberCreateRequest;
use App\Http\Requests\HackathonSubscriberEditRequest;
use App\Http\Requests\HackathonSubscriberIndexRequest;
use App\Http\Requests\StoreHackathonSubscriberRequest;
use App\Http\Requests\UpdateHackathonSubscriberRequest;
use App\Models\Developer;
use App\Models\Hackathon;
use App\Models\HackathonSubscriber;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class HackathonSubscribersController extends Controller
{
    public function index(HackathonSubscriberIndexRequest $request, Hackathon $hackathon): Response
    {
        $subscribers = $hackathon->subscribers()
            ->with('developer:id,name,slug,email')
            ->orderBy('created_at')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'developer' => $s->developer ? [
                    'id' => $s->developer->id,
                    'name' => $s->developer->name,
                    'slug' => $s->developer->slug,
                    'email' => $s->developer->email,
                ] : null,
                'message' => $s->message,
                'status' => $s->status->value,
                'status_label' => $s->status->label(),
                'created_at' => $s->created_at?->toIso8601String(),
            ]);

        return Inertia::render('Hackathons/Dashboard/Subscribers', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
                'start_date' => $hackathon->start_date?->format('Y-m-d'),
                'end_date' => $hackathon->end_date?->format('Y-m-d'),
            ],
            'subscribers' => $subscribers,
        ]);
    }

    public function create(HackathonSubscriberCreateRequest $request, Hackathon $hackathon): Response
    {
        $subscribedDeveloperIds = $hackathon->subscribers()->pluck('developer_id');
        $developers = Developer::query()
            ->whereNotIn('id', $subscribedDeveloperIds)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($d) => ['id' => $d->id, 'name' => $d->name]);

        $statusOptions = array_map(
            fn (HackathonSubscriberStatus $s) => ['value' => $s->value, 'label' => $s->label()],
            HackathonSubscriberStatus::cases()
        );

        return Inertia::render('Hackathons/Dashboard/SubscribersCreate', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
            ],
            'developers' => $developers,
            'statusOptions' => $statusOptions,
            'storeUrl' => route('subscribers.store', $hackathon),
        ]);
    }

    public function store(StoreHackathonSubscriberRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = HackathonSubscriberStatus::from($data['status']);
        $hackathon->subscribers()->create([
            'developer_id' => $data['developer_id'],
            'message' => $data['message'],
            'status' => $data['status'],
        ]);

        return redirect()->route('subscribers.index', $hackathon)
            ->with('success', 'Subscriber added successfully.');
    }

    public function edit(HackathonSubscriberEditRequest $request, Hackathon $hackathon, HackathonSubscriber $subscriber): Response
    {
        if ($subscriber->hackathon_id !== $hackathon->id) {
            abort(404);
        }

        $subscriber->load('developer:id,name,slug,email');

        $statusOptions = array_map(
            fn (HackathonSubscriberStatus $s) => ['value' => $s->value, 'label' => $s->label()],
            HackathonSubscriberStatus::cases()
        );

        return Inertia::render('Hackathons/Dashboard/SubscribersEdit', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
            ],
            'subscriber' => [
                'id' => $subscriber->id,
                'developer' => $subscriber->developer ? [
                    'id' => $subscriber->developer->id,
                    'name' => $subscriber->developer->name,
                    'slug' => $subscriber->developer->slug,
                    'email' => $subscriber->developer->email,
                ] : null,
                'message' => $subscriber->message,
                'status' => $subscriber->status->value,
            ],
            'statusOptions' => $statusOptions,
            'updateUrl' => route('subscribers.update', [$hackathon, $subscriber]),
        ]);
    }

    public function update(UpdateHackathonSubscriberRequest $request, Hackathon $hackathon, HackathonSubscriber $subscriber): RedirectResponse
    {
        if ($subscriber->hackathon_id !== $hackathon->id) {
            abort(404);
        }

        $data = $request->validated();
        $subscriber->update([
            'message' => $data['message'],
            'status' => HackathonSubscriberStatus::from($data['status']),
        ]);

        return redirect()->route('subscribers.index', $hackathon)
            ->with('success', 'Subscriber updated successfully.');
    }
}
