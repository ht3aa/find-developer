<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Find Developer') }} - @yield('title', 'Find Your Perfect Developer')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Custom CSS -->
        <link href="{{ asset('css/developer-search.css') }}" rel="stylesheet">
        
        <!-- Scripts -->
        @vite(['resources/js/app.js'])
        @livewireStyles
    </head>
    <body>
        <!-- Navigation -->
        <nav class="navbar">
            <div class="navbar-container">
                <a href="{{ url('/') }}" class="navbar-brand">
                    FindDeveloper
                </a>
                        <div class="navbar-menu">
                            <a href="{{ route('register') }}" class="navbar-link">
                                Register as Developer
                            </a>
                        </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-container">
                <p class="footer-text">
                    &copy; {{ date('Y') }} FindDeveloper. All rights reserved.
                </p>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
