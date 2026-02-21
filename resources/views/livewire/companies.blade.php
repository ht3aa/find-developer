<div class="companies-container">
    <div class="companies-header">
        <h1 class="companies-title">Companies</h1>
        <p class="companies-subtitle">Explore companies and the skills they are currently working on. These skills are provided by the job offers that the companies have posted.</p>
    </div>

    <!-- Search Bar + Filters (same style as developer search) -->
    <div class="companies-search-bar-container">
        <div class="companies-search-bar-wrapper">
            <div class="companies-search-bar-row">
                <div class="companies-search-icon-wrapper">
                    <svg class="companies-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by company name or skills..."
                    class="companies-search-input"
                    aria-label="Search companies"
                />
            </div>
            <div class="search-bar-actions">
                <div x-data="{ filterPanelOpen: false, scrolled: false }"
                     x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 200; });"
                     class="filter-wrapper">
                    <button
                        @click="filterPanelOpen = true"
                        x-show="!filterPanelOpen"
                        :class="scrolled ? 'floating-filter-btn scrolled' : 'filter-btn-inline'"
                        type="button"
                        aria-label="Open filters"
                    >
                        <svg class="floating-filter-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span class="floating-filter-text">Filters</span>
                        @if($activeFiltersCount > 0)
                            <span class="filter-count-badge">{{ $activeFiltersCount }}</span>
                        @endif
                    </button>
                    <div
                        x-show="filterPanelOpen"
                        x-cloak
                        @click.away="filterPanelOpen = false"
                        @keydown.escape.window="filterPanelOpen = false"
                        x-transition:enter="filter-panel-transition-enter"
                        x-transition:enter-start="filter-panel-enter-start"
                        x-transition:enter-end="filter-panel-enter-end"
                        x-transition:leave="filter-panel-transition-leave"
                        x-transition:leave-start="filter-panel-leave-start"
                        x-transition:leave-end="filter-panel-leave-end"
                        class="modern-filter-panel"
                    >
                        <div class="filter-panel-content" @click.stop>
                            <div class="filter-panel-header">
                                <h3 class="filter-panel-title">Filters</h3>
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
            </div>
        </div>
    </div>

    <!-- Results count -->
    <div class="companies-results-header">
        <span class="companies-results-label">Found</span>
        <span class="companies-results-number">{{ $companies->count() }}</span>
        <span class="companies-results-label">company{{ $companies->count() !== 1 ? 'ies' : '' }}</span>
    </div>

    @if($companies->isEmpty())
        <div class="companies-empty">
            <svg class="companies-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="companies-empty-title">{{ $activeFiltersCount > 0 ? 'No companies match your search or filters' : 'No companies yet' }}</h3>
            <p class="companies-empty-text">{{ $activeFiltersCount > 0 ? 'Try different search terms or clear the filters.' : 'Check back later for companies and their skills.' }}</p>
        </div>
    @else
        <div class="companies-grid">
            @foreach($companies as $company)
                @php
                    $visibleSkills = $company->skills->take(10);
                    $remainingSkills = $company->skills->slice(10);
                    $hasMoreSkills = $remainingSkills->isNotEmpty();
                @endphp
                <div class="company-card" x-data="{ expanded: false }">
                    <div class="company-card-header">
                        <div class="company-logo-wrapper">
                            @if($company->logo_url)
                                <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="company-logo" loading="lazy">
                            @else
                                <div class="company-logo-placeholder">
                                    <span class="company-logo-initial">{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                        <h2 class="company-name">{{ $company->name }}</h2>
                    </div>
                    <div class="company-card-body">
                        @if($company->skills->isNotEmpty())
                            <h3 class="company-skills-title">Skills they work on</h3>
                            <div class="company-skills-list">
                                @foreach($visibleSkills as $skill)
                                    <span class="company-skill-badge">{{ $skill->name }}</span>
                                @endforeach
                                @if($hasMoreSkills)
                                    <span x-show="expanded" x-cloak class="company-skills-more-wrap">
                                        @foreach($remainingSkills as $skill)
                                            <span class="company-skill-badge">{{ $skill->name }}</span>
                                        @endforeach
                                    </span>
                                    <button
                                        type="button"
                                        @click="expanded = !expanded"
                                        class="company-skills-read-more"
                                    >
                                        <span x-show="!expanded">Show {{ $remainingSkills->count() }} more</span>
                                        <span x-show="expanded" x-cloak>Show less</span>
                                    </button>
                                @endif
                            </div>
                        @else
                            <p class="company-no-skills">No skills listed yet.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
