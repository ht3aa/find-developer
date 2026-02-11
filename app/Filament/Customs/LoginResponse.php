<?php

namespace App\Filament\Customs;

use Filament\Auth\Http\Responses\LoginResponse as ResponsesLoginResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends ResponsesLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect()->route('home');
    }
}
