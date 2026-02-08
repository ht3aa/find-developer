<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeveloperLoginRequest;
use App\Repositories\DeveloperAuthRepository;
use Illuminate\Http\Request;

class DeveloperAuthController extends Controller
{
    public function __construct(
        protected DeveloperAuthRepository $authRepository
    ) {}

    /**
     * Show the developer login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('developer-login');
    }

    /**
     * Handle a developer login request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(DeveloperLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        $this->authRepository->attemptLogin($credentials, $remember);

        $request->session()->regenerate();

        return redirect()->route('home');
    }

    /**
     * Handle a developer logout request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->authRepository->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
