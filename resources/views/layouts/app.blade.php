<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <script>
            (function() {
                var theme = localStorage.getItem('theme');
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
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
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/filament/admin/theme.css'])

        <!-- Custom CSS -->
        <link href="{{ asset('css/developer-search.css') }}" rel="stylesheet">
        @stack('styles')

        <!-- Filament Styles -->
        @filamentStyles

        @livewireStyles

        @stack('styles')
    </head>
    <body>
        <!-- Navigation -->
        <nav class="navbar"
             x-data="{ mobileMenuOpen: false, scrolled: false }"
             x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
             :class="{ 'scrolled': scrolled }">
            <div class="navbar-container">
                <div class="navbar-brand-group">
                    <a href="{{ url('/') }}" class="navbar-brand" aria-label="FindDeveloper - Home">
                        <img src="{{ asset('light-logo.svg') }}" alt="FindDeveloper" class="navbar-brand-logo navbar-brand-logo-light" width="120" height="20">
                        <img src="{{ asset('dark-logo.svg') }}" alt="FindDeveloper" class="navbar-brand-logo navbar-brand-logo-dark" width="120" height="20">
                    </a>
                    <a href="https://github.com/ht3aa/find-developer" target="_blank" rel="noopener noreferrer" class="navbar-github-link" aria-label="View source on GitHub" title="View source on GitHub">
                        <svg class="navbar-github-icon" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                            <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/>
                        </svg>
                    </a>
                </div>

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
                    @auth
                        <a href="{{ url('/admin') }}" class="navbar-link" style="border: 1px solid var(--color-primary);" @click="mobileMenuOpen = false">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="navbar-link" style="border: 1px solid var(--color-primary);" @click="mobileMenuOpen = false">
                            Register
                        </a>
                    @endauth
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
                </div>

                <!-- Theme Toggle + Hamburger -->
                <div class="navbar-actions">
                    <button
                        type="button"
                        class="navbar-theme-toggle"
                        onclick="toggleDarkMode()"
                        aria-label="Toggle dark mode"
                        title="Toggle dark mode"
                    >
                        <svg class="dark-mode-icon sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg class="dark-mode-icon moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
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
                </div>
            </div>

            <!-- Navbar Notices -->
            <div class="navbar-notices">
                <div class="navbar-notices-inner">
                    <div class="navbar-notice notice-github">
                        <svg class="navbar-notice-icon" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/>
                        </svg>
                        <span class="navbar-notice-text">
                            <strong>Open Source</strong>
                            <span class="navbar-notice-sep">·</span>
                            Give us a star — it helps us grow!
                        </span>
                        <a href="https://github.com/ht3aa/find-developer" target="_blank" rel="noopener noreferrer" class="navbar-notice-action">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            Star on GitHub
                        </a>
                    </div>
                    <span class="navbar-notice-divider"></span>
                    <div class="navbar-notice notice-email">
                        <svg class="navbar-notice-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0l-9.75 6.5-9.75-6.5"/>
                        </svg>
                        <span class="navbar-notice-text">
                            After registering, check your email for login credentials and updates.
                        </span>
                    </div>
                </div>
            </div>
        </nav>
        <div class="navbar-spacer"></div>

        <!-- Dark mode toggle is now in the navbar -->

        <!-- Page Content -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <!-- Support Us Banner -->
            <div class="support-banner">
                <div class="support-banner-container">
                    <div class="support-banner-content">
                        <!-- Left: Message -->
                        <div class="support-banner-info">
                            <div class="support-banner-heading">
                                <svg class="support-banner-heart" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                                </svg>
                                <h3 class="support-banner-title">Support FindDeveloper</h3>
                            </div>
                            <p class="support-banner-text">
                                If you find this platform useful, consider supporting us. Your contribution helps us market the platform and build new features for the Iraqi developer community.
                            </p>
                        </div>

                        <!-- Right: Payment Card -->
                        <div class="support-banner-card">
                            <div class="support-banner-card-inner">
                                <div class="support-banner-card-label">
                                    <svg class="support-banner-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                    </svg>
                                    <span>Qi Card</span>
                                </div>
                                <div class="support-banner-card-number" onclick="navigator.clipboard.writeText('5862997060')" title="Click to copy">
                                    <span class="support-banner-digits">5862 9970 60</span>
                                    <svg class="support-banner-copy-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9.75a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                                    </svg>
                                </div>
                                <p class="support-banner-card-hint">Iraq's electronic payment card &middot; Click to copy</p>
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
