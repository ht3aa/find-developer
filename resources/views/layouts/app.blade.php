<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <script>document.documentElement.classList.toggle('dark', localStorage.getItem('theme')==='dark');</script>
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

        <!-- Favicon - PNG/ICO first for Google Search (they prefer 48x48+ PNG/ICO over SVG) -->
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('web-app-manifest-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


        <!-- Scripts -->
        @vite(['resources/js/app.js', 'resources/css/filament/admin/theme.css'])

        <!-- Base + Layout + page-specific CSS (from resources/css, copied to public in build) -->
        <link href="{{ asset('css/base.css') }}" rel="stylesheet">
        <link href="{{ asset('css/layout.css') }}" rel="stylesheet">
        @if(request()->routeIs('blog.show'))
            <link href="{{ asset('css/blog.css') }}" rel="stylesheet">
        @elseif(request()->routeIs('blogs'))
            <link href="{{ asset('css/blogs.css') }}" rel="stylesheet">
        @else
            <link href="{{ asset('css/developer-search.css') }}" rel="stylesheet">
        @endif
        @stack('styles')

        <!-- Filament Styles -->
        @filamentStyles

        @livewireStyles
    </head>
    <body>
        <!-- Navigation -->
        <nav class="navbar" x-data="{ mobileMenuOpen: false }">
            <div class="navbar-container">
                <div class="navbar-brand-group">
                    <a href="{{ url('/') }}" class="navbar-brand {{ request()->routeIs('home') ? 'navbar-brand-active' : '' }}" aria-label="FindDeveloper - Home">
                        <img src="{{ asset('light-logo.svg') }}" alt="FindDeveloper" class="navbar-brand-logo navbar-brand-logo-light" width="120" height="20">
                        <img src="{{ asset('dark-logo.svg') }}" alt="FindDeveloper" class="navbar-brand-logo navbar-brand-logo-dark" width="120" height="20">
                    </a>
                    <a href="https://github.com/ht3aa/find-developer" target="_blank" rel="noopener noreferrer" class="navbar-github-link" aria-label="View source on GitHub" title="View source on GitHub">
                        <svg class="navbar-github-icon" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                            <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/>
                        </svg>
                    </a>
                </div>

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
                <div class="navbar-menu" x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false">
                    <a href="{{ route('blogs') }}" class="navbar-link {{ request()->routeIs('blogs') ? 'navbar-link-active' : '' }}" @click="mobileMenuOpen = false">
                        Blogs
                    </a>
                    <a href="{{ route('recommended') }}" class="navbar-link navbar-link-recommended {{ request()->routeIs('recommended') ? 'navbar-link-active' : '' }}" @click="mobileMenuOpen = false">
                        <svg class="navbar-star-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Recommended
                    </a>
                    <a href="{{ route('special-needs-developers') }}" class="navbar-link {{ request()->routeIs('special-needs-developers') ? 'navbar-link-active' : '' }}" @click="mobileMenuOpen = false">
                        Special Needs
                    </a>
                    @auth
                        {{-- Dashboard --}}
                        <a href="{{ url('/admin') }}"
                           class="navbar-link navbar-link-outline {{ request()->is('admin*') ? 'navbar-link-active' : '' }}"
                           @click="mobileMenuOpen = false">
                            Dashboard
                        </a>

                        {{-- Logout --}}
                        <form method="POST"
                              action="{{ route('filament.admin.auth.logout') }}"
                              class="navbar-logout-form">
                            @csrf
                            <button type="submit"
                                    class="navbar-link navbar-link-logout"
                                    @click="mobileMenuOpen = false">
                                Logout
                            </button>
                        </form>

                    @else
                        {{-- Register --}}
                        <a href="{{ route('register') }}"
                           class="navbar-link navbar-link-outline {{ request()->routeIs('register') ? 'navbar-link-active' : '' }}"
                           @click="mobileMenuOpen = false">
                            Register
                        </a>

                        {{-- Login --}}
                        <a href="{{ route('filament.admin.auth.login') }}"
                           class="navbar-link {{ request()->routeIs('filament.admin.auth.login') ? 'navbar-link-active' : '' }}"
                           @click="mobileMenuOpen = false">
                            Login
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Info Banner -->
            <div class="info-banner">
                <div class="info-banner-container">
                    <div class="info-banner-content">
                        <svg class="info-banner-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>
                        <p class="info-banner-text">
                            <strong>Open Source!</strong> FindDeveloper is open source. If you find it useful, give us a star on GitHub — it helps us grow and improve!
                            <a href="https://github.com/ht3aa/find-developer" target="_blank" rel="noopener noreferrer" class="info-banner-link">Star on GitHub →</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Email Check Banner -->
            @guest
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
            @endguest
        </nav>
        <div class="navbar-spacer"></div>

        <!-- Floating Dark Mode Toggle (visible on all pages) -->
        <button
            type="button"
            class="floating-dark-mode-toggle"
            onclick="toggleDarkMode()"
            aria-label="Toggle dark mode"
            title="Toggle dark mode"
        >
            <!-- Sun Icon (Light Mode) -->
            <svg class="dark-mode-icon sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <!-- Moon Icon (Dark Mode) -->
            <svg class="dark-mode-icon moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
        </button>

        <!-- Page Content -->
        <main class="main-content">
            @if(session('home_notification'))
                <div
                    class="flash-notification flash-notification-success"
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                >
                    <div class="flash-notification-inner">
                        <svg class="flash-notification-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div class="flash-notification-body">
                            <p class="flash-notification-title">{{ session('home_notification')['title'] }}</p>
                            <p class="flash-notification-message">{{ session('home_notification')['body'] }}</p>
                        </div>
                        <button type="button" class="flash-notification-dismiss" @click="show = false" aria-label="Dismiss">
                            <svg class="flash-notification-dismiss-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

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
                    <a href="{{ route('plans') }}" class="footer-link">
                        Plans
                    </a>
                    <a href="{{ route('services') }}" class="footer-link">
                        Services
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
