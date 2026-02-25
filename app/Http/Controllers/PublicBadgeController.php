<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Inertia\Inertia;
use Inertia\Response;

class PublicBadgeController extends Controller
{
    /**
     * Display a listing of all active badges for the public.
     */
    public function index(): Response
    {
        $badges = Badge::query()
            ->where('is_active', true)
            ->withCount('developers')
            ->orderBy('name')
            ->get();

        return Inertia::render('Badges/Public', [
            'badges' => $badges,
        ]);
    }
}
