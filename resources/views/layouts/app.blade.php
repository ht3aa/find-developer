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
                    <a href="{{ route('services') }}" class="navbar-link" @click="mobileMenuOpen = false">
                        Services
                    </a>
                    <a href="{{ route('badges') }}" class="navbar-link" @click="mobileMenuOpen = false">
                        Badges
                    </a>
                    <a href="{{ route('jobs') }}" class="navbar-link" @click="mobileMenuOpen = false">
                        Jobs
                    </a>
                    <a href="{{ route('recommended') }}" class="navbar-link navbar-link-recommended" @click="mobileMenuOpen = false">
                        <svg class="navbar-star-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Recommended
                    </a>
                    <a href="{{ route('register') }}" class="navbar-link" @click="mobileMenuOpen = false">
                        Register as Developer
                    </a>
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

        <!-- Developer Promotion Banner -->
        <div class="developer-promotion-banner">
            <div class="developer-promotion-banner-container">
                <div class="developer-promotion-banner-content">
                    <div class="developer-promotion-banner-text-wrapper">
                        <svg class="developer-promotion-banner-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <p class="developer-promotion-banner-text">
                            To make your profile good and increase the chance to hire/getting work you can do it with only <strong>10,000 IQD</strong> 
                        </p>
                    </div>
                    <a href="https://wa.me/9647708246418?text=Hello,%20I%20would%20like%20to%20improve%20my%20developer%20profile" 
                       target="_blank" 
                       class="developer-promotion-banner-button">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>

        @if($currentRoute === 'home')
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
        @endif

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
