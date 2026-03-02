<?php

namespace App\Http\Controllers;

use App\Models\Hackathon;
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
            ->map(fn (Hackathon $h) => [
                'id' => $h->id,
                'title' => $h->title,
                'slug' => $h->slug,
                'body' => $h->body ? Str::limit(strip_tags($h->body), 160) : null,
                'image_url' => $h->image_url,
                'youtube_url' => $h->youtube_url,
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
        $hackathon->load('rewardBadge:id,name,slug,icon,color');

        return Inertia::render('Hackathons/Show', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
                'slug' => $hackathon->slug,
                'body' => $hackathon->body,
                'image_url' => $hackathon->image_url,
                'youtube_url' => $hackathon->youtube_url,
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
            ],
        ]);
    }
}
