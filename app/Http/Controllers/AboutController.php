<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    /**
     * Display the public about page.
     */
    public function __invoke(): Response
    {
        return Inertia::render('About/Index');
    }
}
