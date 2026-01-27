<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $currentRoute = Route::currentRouteName();
            $customSeo = [];
            
            if (View::hasSection('seo_title')) {
                $customSeo['title'] = trim(View::yieldContent('seo_title'));
            }
            if (View::hasSection('seo_description')) {
                $customSeo['description'] = trim(View::yieldContent('seo_description'));
            }
            if (View::hasSection('seo_keywords')) {
                $customSeo['keywords'] = trim(View::yieldContent('seo_keywords'));
            }
            if (View::hasSection('seo_image')) {
                $customSeo['image'] = trim(View::yieldContent('seo_image'));
            }
            
            $customSeo['url'] = url()->current();
            
            $pageSeo = \App\Helpers\SeoHelper::getPageSeo($currentRoute ?? 'home', $customSeo);
        @endphp

        <!-- Primary Meta Tags -->
        <title>{{ $pageSeo['title'] }}</title>
        <meta name="title" content="{{ $pageSeo['title'] }}">
        <meta name="description" content="{{ $pageSeo['description'] }}">
        <meta name="keywords" content="{{ $pageSeo['keywords'] }}">
        <meta name="author" content="FindDeveloper">
        <meta name="robots" content="index, follow">
        <meta name="language" content="English">
        <meta name="revisit-after" content="7 days">
        <link rel="canonical" href="{{ $pageSeo['url'] }}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="{{ $pageSeo['type'] }}">
        <meta property="og:url" content="{{ $pageSeo['url'] }}">
        <meta property="og:title" content="{{ $pageSeo['title'] }}">
        <meta property="og:description" content="{{ $pageSeo['description'] }}">
        <meta property="og:image" content="{{ $pageSeo['image'] }}">
        <meta property="og:site_name" content="{{ $pageSeo['site_name'] }}">
        <meta property="og:locale" content="en_US">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ $pageSeo['url'] }}">
        <meta name="twitter:title" content="{{ $pageSeo['title'] }}">
        <meta name="twitter:description" content="{{ $pageSeo['description'] }}">
        <meta name="twitter:image" content="{{ $pageSeo['image'] }}">

        <!-- Additional SEO -->
        <meta name="theme-color" content="#3b82f6">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        
        <!-- Scripts -->
        @vite(['resources/js/app.js', 'resources/css/filament/admin/theme.css'])

        <!-- Custom CSS -->
        <link href="{{ asset('css/developer-search.css') }}" rel="stylesheet">
        @stack('styles')

        <!-- Filament Styles -->
        @filamentStyles

        @livewireStyles

        @stack('styles')
    </head>
    <body>
        <!-- Email Check Banner -->
        <div class="email-check-banner">
            <div class="email-check-banner-container">
                <div class="email-check-banner-content">
                    <svg class="email-check-banner-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <p class="email-check-banner-text">
                        <strong>Important:</strong> After registering as a developer, please check the email address you registered with. We will send important updates and login credentials to that email.
                    </p>
                </div>
            </div>
        </div>

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
                    <a href="{{ route('services') }}" class="navbar-link" @click="mobileMenuOpen = false">
                        Services
                    </a>
                    <a href="{{ route('experience-tasks') }}" class="navbar-link" @click="mobileMenuOpen = false">
                        Get Experience
                    </a>
                    <a href="{{ route('recommended') }}" class="navbar-link navbar-link-recommended" @click="mobileMenuOpen = false">
                        <svg class="navbar-star-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Recommended
                    </a>
                    <a href="{{ route('register') }}" class="navbar-link" style="border: 1px solid var(--color-primary);" @click="mobileMenuOpen = false">
                        Register
                    </a>
                    @auth
                        @if(auth()->user()->isDeveloper())
                            <form method="POST" action="{{ route('developer.logout') }}" class="navbar-logout-form">
                                @csrf
                                <button type="submit" class="navbar-link navbar-link-logout" @click="mobileMenuOpen = false">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('developer.login') }}" class="navbar-link" @click="mobileMenuOpen = false">
                                Login
                            </a>
                        @endif
                    @else
                        <a href="{{ route('developer.login') }}" class="navbar-link" @click="mobileMenuOpen = false">
                             Login
                        </a>
                    @endauth
                    <!-- Dark Mode Toggle -->
                    <button 
                        type="button"
                        class="dark-mode-toggle"
                        onclick="toggleDarkMode()"
                        aria-label="Toggle dark mode"
                        title="Toggle dark mode"
                    >
                        <!-- Sun Icon (Light Mode) -->
                        <svg class="dark-mode-icon sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <!-- Moon Icon (Dark Mode) -->
                        <svg class="dark-mode-icon moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </nav>

            <!-- Hosting Plan Promotional Banner -->
            <div class="info-banner">
                <div class="info-banner-container">
                    <div class="info-banner-content">
                        <svg class="info-banner-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>
                        <p class="info-banner-text">
                            <strong>🚀 Host Your Portfolio!</strong> Get your own subdomain (like <a href="https://hasan-tahseen.find-developer.com/" target="_blank" style="color: var(--color-primary); text-decoration: underline; font-weight: 600;">hasan-tahseen.find-developer.com</a>) with full support for just <strong>5,000 IQD/month</strong>. 
                            <a href="{{ route('plans') }}" style="color: var(--color-primary); text-decoration: underline; font-weight: 600; margin-left: 0.5rem;">View Plans →</a>
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
            <!-- Developer Promotion Banner -->
            <div class="developer-promotion-banner">
                <div class="developer-promotion-banner-container">
                    <div class="developer-promotion-banner-content">
                        <div class="developer-promotion-banner-text-wrapper">
                            <svg class="developer-promotion-banner-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <div class="developer-promotion-banner-text-block">
                                <p class="developer-promotion-banner-text">
                                    If You find this platform useful, you can support us by sponsoring us or donating to us. Qi Card Number is <strong>5862997060</strong>
                                </p>
                                <p class="developer-promotion-banner-note">
                                    We will use the money to improve by marketing the platform and add new features.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-container">
                <div class="footer-links">
                    <a href="{{ route('about') }}" class="footer-link">
                        About Us
                    </a>
                    <a href="{{ route('charts') }}" class="footer-link">
                        Charts
                    </a>
                    <a href="{{ route('badges') }}" class="footer-link">
                        Badges
                    </a>
                </div>
                <p class="footer-text">
                    &copy; {{ date('Y') }} FindDeveloper. All rights reserved.
                </p>
            </div>
        </footer>

        <!-- Structured Data (JSON-LD) -->
        <script type="application/ld+json">
        {!! json_encode(\App\Helpers\SeoHelper::getOrganizationStructuredData(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
        <script type="application/ld+json">
        {!! json_encode(\App\Helpers\SeoHelper::getWebsiteStructuredData(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
        @hasSection('structured_data')
            <script type="application/ld+json">
            @yield('structured_data')
            </script>
        @endif

        @livewireScripts
        @filamentScripts
    </body>
</html>
