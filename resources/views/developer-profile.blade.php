@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', $developer->name . ' - Developer Profile')
@section('seo_title', $developer->name . ' - ' . $developer->jobTitle->name . ' | FindDeveloper')
@section('seo_description', $developer->bio ? Str::limit($developer->bio, 160) : 'View ' . $developer->name . '\'s developer profile. ' . $developer->jobTitle->name . ' with ' . $developer->years_of_experience . ' years of experience.')
@section('seo_keywords', $developer->name . ', ' . $developer->jobTitle->name . ', developer, ' . $developer->skills->pluck('name')->implode(', '))

@section('content')
<div class="dev-profile-page">
    <div class="dev-profile-container">

        <!-- Back Link -->
        <a href="{{ route('home') }}" class="dev-profile-back">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Search
        </a>

        <!-- Hero Section -->
        <div class="dev-profile-hero">
            <div class="dev-profile-hero-content">
                <!-- Name & Title -->
                <div class="dev-profile-identity">
                    <h1 class="dev-profile-name">{{ $developer->name }}</h1>
                    <span class="dev-profile-title">{{ $developer->jobTitle->name }}</span>
                </div>

                <!-- Badges -->
                @if($developer->badges->count() > 0)
                    <div class="dev-profile-badges">
                        @foreach($developer->badges as $badge)
                            <a href="{{ route('badges') }}" class="dev-profile-badge" @if($badge->color) style="background: {{ $badge->color }}15; border-color: {{ $badge->color }}40; color: {{ $badge->color }};" @endif>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                                {{ $badge->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Subscription badge -->
                @if($developer->isPremium())
                    <span class="dev-profile-plan dev-profile-plan-premium">
                        <svg fill="currentColor" viewBox="0 0 20 20" width="16" height="16">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Premium Developer
                    </span>
                @elseif($developer->recommended_by_us)
                    <span class="dev-profile-plan dev-profile-plan-recommended">
                        <svg fill="currentColor" viewBox="0 0 20 20" width="16" height="16">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Recommended By Us
                    </span>
                @endif
            </div>

            <!-- Quick Stats -->
            <div class="dev-profile-stats">
                <div class="dev-profile-stat">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="22" height="22">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <div class="dev-profile-stat-info">
                        <span class="dev-profile-stat-value">{{ $developer->years_of_experience }}</span>
                        <span class="dev-profile-stat-label">Years Experience</span>
                    </div>
                </div>

                @if($developer->location)
                <div class="dev-profile-stat">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="22" height="22">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div class="dev-profile-stat-info">
                        <span class="dev-profile-stat-value">{{ $developer->location->getLabel() }}</span>
                        <span class="dev-profile-stat-label">Location</span>
                    </div>
                </div>
                @endif

                <div class="dev-profile-stat">
                    @if($developer->is_available)
                        <svg fill="currentColor" viewBox="0 0 20 20" width="22" height="22" style="color: #10b981;">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div class="dev-profile-stat-info">
                            <span class="dev-profile-stat-value" style="color: #10b981;">Available</span>
                            <span class="dev-profile-stat-label">Status</span>
                        </div>
                    @else
                        <svg fill="currentColor" viewBox="0 0 20 20" width="22" height="22" style="color: #ef4444;">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div class="dev-profile-stat-info">
                            <span class="dev-profile-stat-value" style="color: #ef4444;">Not Available</span>
                            <span class="dev-profile-stat-label">Status</span>
                        </div>
                    @endif
                </div>

                <div class="dev-profile-stat">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="22" height="22">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <div class="dev-profile-stat-info">
                        <span class="dev-profile-stat-value">{{ $developer->projects_count }}</span>
                        <span class="dev-profile-stat-label">Projects</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="dev-profile-grid">

            <!-- Left Column -->
            <div class="dev-profile-main">

                <!-- About Section -->
                @if($developer->bio)
                <div class="dev-profile-section">
                    <h2 class="dev-profile-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        About
                    </h2>
                    <p class="dev-profile-bio">{{ $developer->bio }}</p>
                </div>
                @endif

                <!-- Skills Section -->
                @if($developer->skills->count() > 0)
                <div class="dev-profile-section">
                    <h2 class="dev-profile-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        Skills & Technologies
                    </h2>
                    <div class="dev-profile-skills">
                        @foreach($developer->skills as $skill)
                            <span class="dev-profile-skill">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Availability Types -->
                @if($developer->availability_type && count($developer->availability_type) > 0)
                <div class="dev-profile-section">
                    <h2 class="dev-profile-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Availability
                    </h2>
                    <div class="dev-profile-availability-types">
                        @foreach($developer->availability_type as $type)
                            <span class="dev-profile-availability-badge availability-type-{{ $type->value }}">
                                {{ $type->getLabel() }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Salary Section (admin only) -->
                @auth
                    @if((auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) && ($developer->expected_salary_from || $developer->expected_salary_to))
                    <div class="dev-profile-section">
                        <h2 class="dev-profile-section-title">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Expected Salary
                        </h2>
                        <p class="dev-profile-salary">
                            @if($developer->expected_salary_from && $developer->expected_salary_to)
                                {{ number_format($developer->expected_salary_from) }} - {{ number_format($developer->expected_salary_to) }} {{ $developer->currency }}/month
                            @elseif($developer->expected_salary_from)
                                From {{ number_format($developer->expected_salary_from) }} {{ $developer->currency }}/year
                            @else
                                Up to {{ number_format($developer->expected_salary_to) }} {{ $developer->currency }}/year
                            @endif
                        </p>
                    </div>
                    @endif
                @endauth

                <!-- Portfolio Projects Section -->
                @if($developer->projects->count() > 0)
                <div class="dev-profile-section">
                    <h2 class="dev-profile-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        Portfolio & Projects
                    </h2>
                    <div class="dev-profile-projects-grid">
                        @foreach($developer->projects as $project)
                            <div class="dev-profile-project-card">
                                <h3 class="dev-profile-project-title">{{ $project->title }}</h3>
                                @if($project->description)
                                    <p class="dev-profile-project-desc">{{ $project->description }}</p>
                                @endif
                                @if($project->link)
                                    <a href="{{ $project->link }}" target="_blank" class="dev-profile-project-link">
                                        View Project
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Recommendations Section -->
                @if($developer->recommendationsReceived->count() > 0)
                <div class="dev-profile-section">
                    <h2 class="dev-profile-section-title">
                        <svg fill="currentColor" viewBox="0 0 20 20" width="20" height="20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Recommendations ({{ $developer->recommendationsReceived->count() }})
                    </h2>
                    <div class="dev-profile-recommendations">
                        @foreach($developer->recommendationsReceived as $recommendation)
                            <div class="dev-profile-recommendation-card">
                                <div class="dev-profile-recommendation-header">
                                    <div class="dev-profile-recommendation-author">
                                        <div class="dev-profile-recommendation-avatar">
                                            {{ strtoupper(substr($recommendation->recommender->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="dev-profile-recommendation-name">{{ $recommendation->recommender->name ?? 'Anonymous' }}</p>
                                            @if($recommendation->recommender && $recommendation->recommender->jobTitle)
                                                <p class="dev-profile-recommendation-role">{{ $recommendation->recommender->jobTitle->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($recommendation->recommendation_note)
                                    <p class="dev-profile-recommendation-text">"{{ $recommendation->recommendation_note }}"</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Sidebar -->
            <div class="dev-profile-sidebar">

                <!-- Contact Card -->
                <div class="dev-profile-sidebar-card">
                    <h3 class="dev-profile-sidebar-title">Get In Touch</h3>
                    <div class="dev-profile-contact-links">
                        <a href="mailto:{{ $developer->email }}" class="dev-profile-contact-btn dev-profile-contact-email">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Send Email
                        </a>

                        @if($developer->portfolio_url)
                        <a href="{{ $developer->portfolio_url }}" target="_blank" class="dev-profile-contact-btn dev-profile-contact-portfolio">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                            Portfolio Website
                        </a>
                        @endif

                        @if($developer->github_url)
                        <a href="{{ $developer->github_url }}" target="_blank" class="dev-profile-contact-btn dev-profile-contact-github">
                            <svg fill="currentColor" viewBox="0 0 24 24" width="18" height="18">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                            GitHub Profile
                        </a>
                        @endif

                        @if($developer->linkedin_url)
                        <a href="{{ $developer->linkedin_url }}" target="_blank" class="dev-profile-contact-btn dev-profile-contact-linkedin">
                            <svg fill="currentColor" viewBox="0 0 24 24" width="18" height="18">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                            LinkedIn
                        </a>
                        @endif
                    </div>
                </div>

                @if($developer->cv_path_url)
                <!-- CV / Resume Card -->
                <div class="dev-profile-sidebar-card">
                    <h3 class="dev-profile-sidebar-title">CV / Resume</h3>
                    <div class="dev-profile-cv-section">
                        <a href="{{ $developer->cv_path_url }}" target="_blank" rel="noopener noreferrer" class="dev-profile-cv-btn">
                            <svg class="dev-profile-cv-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>View CV / Resume</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Recommend This Developer -->
                <div class="dev-profile-sidebar-card">
                    <h3 class="dev-profile-sidebar-title">Recommend</h3>
                    <x-developer-recommendation-button :developer="$developer" />
                </div>

                <!-- Quick Info Card -->
                <div class="dev-profile-sidebar-card">
                    <h3 class="dev-profile-sidebar-title">Quick Info</h3>
                    <div class="dev-profile-quick-info">
                        <div class="dev-profile-info-row">
                            <span class="dev-profile-info-label">Role</span>
                            <span class="dev-profile-info-value">{{ $developer->jobTitle->name }}</span>
                        </div>
                        <div class="dev-profile-info-row">
                            <span class="dev-profile-info-label">Experience</span>
                            <span class="dev-profile-info-value">{{ $developer->years_of_experience }} years</span>
                        </div>
                        @if($developer->location)
                        <div class="dev-profile-info-row">
                            <span class="dev-profile-info-label">Location</span>
                            <span class="dev-profile-info-value">{{ $developer->location->getLabel() }}</span>
                        </div>
                        @endif
                        <div class="dev-profile-info-row">
                            <span class="dev-profile-info-label">Availability</span>
                            <span class="dev-profile-info-value">
                                @if($developer->is_available)
                                    <span style="color: #10b981;">Available</span>
                                @else
                                    <span style="color: #ef4444;">Not Available</span>
                                @endif
                            </span>
                        </div>
                        @if($developer->availability_type && count($developer->availability_type) > 0)
                        <div class="dev-profile-info-row">
                            <span class="dev-profile-info-label">Work Type</span>
                            <span class="dev-profile-info-value">
                                {{ collect($developer->availability_type)->map(fn($t) => $t->getLabel())->implode(', ') }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('styles')
    <link href="{{ asset('css/developer-profile.css') }}" rel="stylesheet">
@endpush
@endsection
