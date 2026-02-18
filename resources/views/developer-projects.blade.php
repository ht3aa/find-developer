@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', $developer->name . ' - Projects')
@section('seo_title', $developer->name . ' - Portfolio Projects | FindDeveloper')
@section('seo_description', 'View all portfolio projects by ' . $developer->name . '. ' . ($developer->bio ? Str::limit($developer->bio, 150) : 'Experienced ' . $developer->jobTitle->name . ' with ' . $developer->years_of_experience . ' years of experience.'))
@section('seo_keywords', $developer->name . ', developer portfolio, projects, ' . $developer->jobTitle->name . ', ' . $developer->skills->pluck('name')->implode(', '))

@section('content')
<div class="developer-projects-page">
    <div class="modern-container">
        <!-- Developer Header -->
        <div class="developer-header-section">
            <div class="developer-header-content">
                <a href="{{ route('home') }}" class="back-link">
                    <svg class="back-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Search
                </a>
                
                <div class="developer-info-card">
                    <div class="developer-info-main">
                        <h1 class="developer-name-large">{{ $developer->name }}</h1>
                        <div class="developer-meta">
                            <span class="job-title-badge-large">{{ $developer->jobTitle->name }}</span>
                            @if($developer->location)
                                <span class="location-badge">
                                    <svg class="location-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $developer->location->getLabel() }}
                                </span>
                            @endif
                            <span class="experience-badge">
                                <svg class="experience-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $developer->years_of_experience }} years
                            </span>
                        </div>
                        
                        @if($developer->bio)
                            <p class="developer-bio-large">{{ $developer->bio }}</p>
                        @endif
                    </div>
                    
                    <div class="developer-contact-info">
                        <a href="mailto:{{ $developer->email }}" class="contact-button email-button">
                            <svg class="contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Email
                        </a>
                        
                        @if($developer->portfolio_url)
                            <a href="{{ $developer->portfolio_url }}" target="_blank" class="contact-button portfolio-button">
                                <svg class="contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                                Portfolio
                            </a>
                        @endif
                        
                        @if($developer->github_url)
                            <a href="{{ $developer->github_url }}" target="_blank" class="contact-button github-button">
                                <svg class="contact-icon" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                </svg>
                                GitHub
                            </a>
                        @endif
                        
                        @if($developer->linkedin_url)
                            <a href="{{ $developer->linkedin_url }}" target="_blank" class="contact-button linkedin-button">
                                <svg class="contact-icon" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                                LinkedIn
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects Section -->
        <div class="projects-section">
            <div class="projects-header">
                <h2 class="projects-title">Portfolio Projects</h2>
                <p class="projects-count">{{ $developer->projects->count() }} {{ Str::plural('project', $developer->projects->count()) }}</p>
            </div>

            @if($developer->projects->count() > 0)
                <div class="projects-grid">
                    @foreach($developer->projects as $project)
                        <div class="project-card">
                            <div class="project-card-header">
                                <h3 class="project-title">{{ $project->title }}</h3>
                            </div>
                            
                            @if($project->description)
                                <div class="project-description">
                                    <p>{{ $project->description }}</p>
                                </div>
                            @endif
                            
                            @if($project->link)
                                <div class="project-actions">
                                    <a href="{{ $project->link }}" target="_blank" rel="noopener noreferrer" class="project-link-button">
                                        <span>View Project</span>
                                        <svg class="project-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="projects-empty">
                    <svg class="projects-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="projects-empty-title">No Projects Yet</h3>
                    <p class="projects-empty-text">This developer hasn't added any portfolio projects yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
    @vite(['resources/css/developer-projects.css'])
@endpush
@endsection
