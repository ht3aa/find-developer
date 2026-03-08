<?php

namespace App\Http\Controllers;

use App\Enums\HackathonSubscriberStatus;
use App\Models\Developer;
use App\Models\Hackathon;
use App\Models\HackathonTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PublicHackathonController extends Controller
{
    /**
     * Display a listing of all hackathons (public).
     */
    public function index(Request $request): Response
    {

        $hackathons = Hackathon::query()
            ->with('rewardBadge:id,name,slug,icon,color')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn(Hackathon $h) => [
                'id' => $h->id,
                'title' => $h->title,
                'slug' => $h->slug,
                'body' => $h->body ? Str::limit(strip_tags($h->body), 160) : null,
                'image_url' => $h->image_url,
                'youtube_url' => $h->youtube_url,
                'youtube_video_id' => $h->youtube_video_id,
                'reward_badge_id' => $h->reward_badge_id,
                'reward_badge' => $h->rewardBadge ? [
                    'id' => $h->rewardBadge->id,
                    'name' => $h->rewardBadge->name,
                    'slug' => $h->rewardBadge->slug,
                    'icon' => $h->rewardBadge->icon,
                    'color' => $h->rewardBadge->color,
                ] : null,
                'reward_description' => $h->reward_description,
                'start_date' => $h->start_date?->toDateString(),
                'end_date' => $h->end_date?->toDateString(),
                'created_at' => $h->created_at?->toIso8601String(),
            ]);

        return Inertia::render('Hackathons/Index', [
            'hackathons' => $hackathons,
        ]);
    }

    /**
     * Display the specified hackathon (public).
     */
    public function show(Request $request, Hackathon $hackathon): Response
    {
        $hackathon->load(['rewardBadge:id,name,slug,icon,color', 'teams']);

        $user = $request->user();
        $canSubscribe = $user?->can('viewDeveloperProfile', Developer::class) ?? false;
        $alreadySubscribed = false;
        if ($canSubscribe && $user->developer) {
            $alreadySubscribed = $hackathon->subscribers()
                ->where('developer_id', $user->developer->id)
                ->exists();
        }

        return Inertia::render('Hackathons/Show', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
                'slug' => $hackathon->slug,
                'body' => $hackathon->body,
                'image_url' => $hackathon->image_url,
                'youtube_url' => $hackathon->youtube_url,
                'youtube_video_id' => $hackathon->youtube_video_id,
                'reward_badge_id' => $hackathon->reward_badge_id,
                'reward_badge' => $hackathon->rewardBadge ? [
                    'id' => $hackathon->rewardBadge->id,
                    'name' => $hackathon->rewardBadge->name,
                    'slug' => $hackathon->rewardBadge->slug,
                    'icon' => $hackathon->rewardBadge->icon,
                    'color' => $hackathon->rewardBadge->color,
                ] : null,
                'reward_description' => $hackathon->reward_description,
                'start_date' => $hackathon->start_date?->toDateString(),
                'end_date' => $hackathon->end_date?->toDateString(),
                'created_at' => $hackathon->created_at?->toIso8601String(),
                'has_teams' => $hackathon->teams->isNotEmpty(),
                'teams_url' => $hackathon->teams->isNotEmpty()
                    ? route('hackathons.teams.public', $hackathon->slug)
                    : null,
            ],
            'canSubscribe' => $canSubscribe,
            'alreadySubscribed' => $alreadySubscribed,
            'subscribersCount' => $hackathon->subscribers()->count(),
            'subscribeUrl' => route('hackathons.subscribe', $hackathon->slug),
            'subscribersUrl' => route('hackathons.subscribers.public', $hackathon->slug),
        ]);
    }

    /**
     * Display the teams and members for the specified hackathon (public).
     */
    public function teams(Request $request, Hackathon $hackathon): Response
    {
        $user = $request->user();
        $developerId = $user?->developer?->id;
        $isSubscriber = $developerId
            ? $hackathon->subscribers()->where('developer_id', $developerId)->exists()
            : false;
        $enableVoting = (bool) $hackathon->enable_voting;

        $teams = $hackathon->teams()
            ->when($hackathon->current_team_id_to_vote, function ($query) use ($hackathon) {
                $query->where('id', $hackathon->current_team_id_to_vote);
            })
            ->with(['votes', 'members.developer:id,name,slug,email'])
            ->withCount(['votes as votes_count' => fn($q) => $q->where('is_voted', true)])
            ->orderByDesc('votes_count')
            ->orderBy('title')
            ->get()
            ->map(fn(HackathonTeam $team) => [
                'id' => $team->id,
                'title' => $team->title,
                'logo_url' => $team->logo_url,
                'votes_count' => $team->votes_count,
                'has_voted' => $developerId
                    ? $team->votes
                    ->where('subscriber_developer_id', $developerId)
                    ->where('is_voted', true)
                    ->isNotEmpty()
                    : false,
                'vote_url' => route('hackathons.teams.vote', [$hackathon->slug, $team]),
                'members' => $team->members
                    ->map(fn($member) => [
                        'id' => $member->id,
                        'position' => $member->position->value,
                        'position_label' => $member->position->label(),
                        'developer' => $member->developer ? [
                            'id' => $member->developer->id,
                            'name' => $member->developer->name,
                            'slug' => $member->developer->slug,
                            'email' => $member->developer->email,
                        ] : null,
                    ])
                    ->values()
                    ->all(),
            ])
            ->values();

        return Inertia::render('Hackathons/Teams', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
                'slug' => $hackathon->slug,
                'enable_voting' => $enableVoting,
            ],
            'teams' => $teams,
            'canVote' => $enableVoting && $isSubscriber,
        ]);
    }

    /**
     * Display the subscribed developers for the specified hackathon (public).
     */
    public function subscribers(Request $request, Hackathon $hackathon): Response
    {
        $developerIds = $hackathon->subscribers()
            ->where('status', HackathonSubscriberStatus::Confirmed)
            ->pluck('developer_id')
            ->all();

        return Inertia::render('Hackathons/Subscribers', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
                'slug' => $hackathon->slug,
            ],
            'developerIds' => $developerIds,
        ]);
    }
}
