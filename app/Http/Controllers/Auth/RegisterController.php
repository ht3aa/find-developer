<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\JobTitle;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\RegisterResponse;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function show(): Response
    {
        return Inertia::render('auth/Register', [
            'jobTitles' => JobTitle::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Create a new user (and developer profile).
     */
    public function store(Request $request, CreatesNewUsers $creator): RedirectResponse|RegisterResponse
    {
        event(new Registered($user = $creator->create($request->all())));

        Auth::guard(config('fortify.guard'))->login($user);

        return app(RegisterResponse::class)->toResponse($request);
    }
}
