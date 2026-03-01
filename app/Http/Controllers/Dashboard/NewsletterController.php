<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NewsletterController extends Controller
{
    /**
     * Display a listing of newsletter subscribers (super admin only).
     */
    public function index(Request $request): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $subscribers = Newsletter::query()
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Newsletter $n) => [
                'id' => $n->id,
                'email' => $n->email,
                'subscribed_at' => $n->created_at->toIso8601String(),
            ]);

        return Inertia::render('Newsletter/Index', [
            'subscribers' => $subscribers,
        ]);
    }
}
