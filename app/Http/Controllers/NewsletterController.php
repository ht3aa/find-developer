<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeNewsletterRequest;
use App\Models\Newsletter;
use Illuminate\Http\RedirectResponse;

class NewsletterController extends Controller
{
    public function store(SubscribeNewsletterRequest $request): RedirectResponse
    {
        Newsletter::create($request->validated());

        return redirect()->back()->with('success', 'Thanks! You have been subscribed to our newsletter.');
    }
}
