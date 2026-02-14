<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as FilamentLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends FilamentLogin
{
    public function getSubheading(): string|Htmlable|null
    {
        $email = config('app.contact_email');

        return new HtmlString('<p class="text-sm text-center text-gray-500">Contact the admin to get access to the admin panel, email: <a class="text-blue-500 hover:text-blue-700" href="mailto:'.$email.'">'.$email.'</a></p>');
    }
}
