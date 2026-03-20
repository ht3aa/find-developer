<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PrivacyPolicyController extends Controller
{
    /**
     * Display the public privacy policy page.
     */
    public function __invoke(): Response
    {
        return Inertia::render('Legal/PrivacyPolicy');
    }
}
