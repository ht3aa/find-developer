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

        
        <!-- Scripts -->
        @vite(['resources/js/app.js', 'resources/css/filament/admin/theme.css'])

        <!-- Custom CSS -->
        <link href="{{ asset('css/developer-search.css') }}" rel="stylesheet">

        <!-- Filament Styles -->
        @filamentStyles

        @livewireStyles
    </head>
    <body>
        <!-- Navigation -->
        <nav class="navbar" x-data="{ mobileMenuOpen: false }">
            <div class="navbar-container">
                <a href="{{ url('/') }}" class="navbar-brand">
                    FindDeveloper
                </a>
                
                <!-- Hamburger Button -->
                <button 
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="navbar-toggle"
                    type="button"
                    aria-label="Toggle navigation menu"
                    aria-expanded="false"
                    x-bind:aria-expanded="mobileMenuOpen"
                >
                    <span class="navbar-toggle-icon" x-bind:class="{ 'active': mobileMenuOpen }">
                        <span class="navbar-toggle-line"></span>
                        <span class="navbar-toggle-line"></span>
                        <span class="navbar-toggle-line"></span>
                    </span>
                </button>

                <!-- Navigation Menu -->
                <div 
                    class="navbar-menu"
                    x-show="mobileMenuOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    @click.away="mobileMenuOpen = false"
                >
                    <a href="{{ route('plans') }}" class="navbar-link" @click="mobileMenuOpen = false">
                        Plans
                    </a>
                    <a href="{{ route('about') }}" class="navbar-link" @click="mobileMenuOpen = false">
                        About Us
                    </a>
                    <a href="{{ route('register') }}" class="navbar-link" @click="mobileMenuOpen = false">
                        Register as Developer
                    </a>
                </div>
            </div>
        </nav>

        <!-- Info Banner -->
        <div class="info-banner">
            <div class="info-banner-container">
                <div class="info-banner-content">
                    <svg class="info-banner-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="info-banner-text">
                        After the registration as a developer, the admin will view your profile and accept. So stay toon!
                    </p>
                </div>
            </div>
        </div>

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
        @filamentScripts
    </body>
</html>
