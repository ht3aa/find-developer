<div class="modern-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <div class="header-content">
            <h1 class="modern-title">Job Opportunities</h1>
            <p class="modern-subtitle">Find your next career opportunity</p>
            <a href="{{ route('post-job') }}" class="hero-register-btn">
                <span>Post a Job</span>
                <svg class="hero-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Modern Search Bar -->
    <div class="search-bar-container">
        <div class="search-bar-wrapper">
            <div class="search-icon-wrapper">
                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Search by job title, company, description..."
                class="search-input"
            />
        </div>
    </div>

    <!-- Floating Filter Button & Panel -->
    <div x-data="{ filterPanelOpen: false }" class="filter-wrapper">
        <!-- Floating Filter Button -->
        <button 
            @click="filterPanelOpen = true"
            x-show="!filterPanelOpen"
            x-transition
            class="floating-filter-btn"
            type="button"
            aria-label="Open filters"
        >
            <svg class="floating-filter-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <span class="floating-filter-text">Filters</span>
        </button>

        <!-- Modern Filter Panel -->
        <div 
            x-show="filterPanelOpen"
            x-cloak
            @click.away="filterPanelOpen = false"
            @keydown.escape.window="filterPanelOpen = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="modern-filter-panel"
        >
            <div class="filter-panel-content" @click.stop>
                <div class="filter-panel-header">
                    <h3 class="filter-panel-title">Advanced Filters</h3>
                    <button 
                        @click="filterPanelOpen = false"
                        class="filter-close-btn"
                        type="button"
                        aria-label="Close filters"
                    >
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form wire:submit.prevent class="filter-form">
                    {{ $this->form }}
                </form>
                <div class="filter-panel-footer">
                    <button 
                        type="button"
                        wire:click="clearFilters"
                        class="clear-filters-btn"
                    >
                        <svg class="clear-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear All Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Count & Active Filters -->
    <div class="results-header">
        <div class="results-count-modern">
            <span class="results-label">Found</span>
            <span class="results-number">{{ $totalCount }}</span>
            <span class="results-label">job{{ $totalCount !== 1 ? 's' : '' }}</span>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="modern-loading">
        <div class="loading-spinner"></div>
        <span class="loading-text">Searching jobs...</span>
    </div>

    <!-- Jobs Grid -->
    @if($jobs->count() > 0)
        <div class="jobs-grid-modern">
            @foreach($jobs as $job)
                <div class="job-card-modern">
                    <div class="job-card-header-modern">
                        <h2 class="job-card-title-modern">{{ $job->title }}</h2>
                        @if($job->jobTitle)
                            <span class="job-badge-modern">{{ $job->jobTitle->name }}</span>
                        @endif
                    </div>
                    
                    <div class="job-card-body-modern">
                        <div class="job-info-row">
                            <div class="job-info-item">
                                <svg class="job-info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span><strong>Company:</strong> {{ $job->company_name }}</span>
                            </div>
                            
                            @if($job->location)
                                <div class="job-info-item">
                                    <svg class="job-info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span><strong>Location:</strong> {{ $job->location->getLabel() }}</span>
                                </div>
                            @endif

                            @if($job->salary_from || $job->salary_to)
                                <div class="job-info-item">
                                    <svg class="job-info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Salary:</strong>
                                        @if($job->salary_from && $job->salary_to)
                                            {{ number_format($job->salary_from) }} - {{ number_format($job->salary_to) }} {{ $job->salary_currency?->value ?? 'IQD' }}
                                        @elseif($job->salary_from)
                                            From {{ number_format($job->salary_from) }} {{ $job->salary_currency?->value ?? 'IQD' }}
                                        @elseif($job->salary_to)
                                            Up to {{ number_format($job->salary_to) }} {{ $job->salary_currency?->value ?? 'IQD' }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="job-description-modern">
                            {{ \Illuminate\Support\Str::limit(strip_tags($job->description), 200) }}
                        </div>

                        @if($job->requirements)
                            <div class="job-requirements-modern">
                                <strong>Requirements:</strong>
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($job->requirements), 150) }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="job-card-footer-modern">
                        <a href="mailto:{{ $job->email }}" class="job-contact-btn">
                            <svg class="job-contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Contact: {{ $job->email }}
                        </a>
                        <div class="job-date-modern">
                            Posted {{ $job->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Modern Pagination -->
        <x-pagination-custom :paginator="$jobs" />
    @endif

    <!-- Modern Empty State -->
    @if($totalCount == 0)
        <div class="empty-state-modern">
            <div class="empty-icon-wrapper">
                <svg class="empty-icon-modern" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <h3 class="empty-title-modern">No jobs found</h3>
            <p class="empty-text-modern">Try adjusting your search criteria or clearing filters to find more results.</p>
            <button 
                type="button"
                wire:click="clearFilters"
                class="empty-action-btn"
            >
                Clear All Filters
            </button>
        </div>
    @endif
</div>
