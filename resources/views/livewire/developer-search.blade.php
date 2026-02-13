<div class="modern-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <div class="header-content">
            <h1 class="modern-title">Find Your Perfect Developer</h1>
            <p class="modern-subtitle">Discover talented developers ready to bring your projects to life</p>
            <a href="{{ route('register') }}" class="hero-register-btn">
                <span>Register as Developer</span>
                <svg class="hero-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
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
                placeholder="Search by name, email, location, or skills..."
                class="search-input"
            />
            <!-- Filter Button Next to Search -->
            <div x-data="{ filterPanelOpen: false, scrolled: false }" 
                 x-init="
                    window.addEventListener('scroll', () => {
                        scrolled = window.scrollY > 200;
                    });
                 "
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
    </div>

    <!-- Results Count & Active Filters -->
    <div class="results-header">
        <div class="results-count-modern">
            <span class="results-label">Found</span>
            <span class="results-number">{{ $totalCount }}</span>
            <span class="results-label">developer{{ $totalCount !== 1 ? 's' : '' }}</span>
        </div>
        <div class="results-order-info">
            <svg class="order-info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="order-info-text">Results are ordered by number of recommendations and badges</span>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="modern-loading">
        <div class="loading-spinner"></div>
        <span class="loading-text">Searching developers...</span>
    </div>

    <!-- Premium Developers Section -->
    @if($premiumDevelopers->count() > 0)
        <div class="modern-section">
            <div class="section-header-modern">
                <div class="section-title-wrapper">
                    <div class="section-icon-wrapper premium">
                        <svg class="section-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="section-title-modern">Must to have Developers</h2>
                        <p class="section-subtitle-modern">Must to have developers with premium profiles</p>
                    </div>
                </div>
            </div>
            <div class="developers-grid-modern">
                @foreach($premiumDevelopers as $developer)
                    <x-developer-card :developer="$developer" :currentUserDeveloper="$currentUserDeveloper" :recommendedDeveloperIds="$recommendedDeveloperIds" />
                @endforeach
            </div>
        </div>
    @endif

    <!-- Pro Developers Section -->
    @if($proDevelopers->count() > 0)
        <div class="modern-section">
            <div class="section-header-modern">
                <div class="section-title-wrapper">
                    <div class="section-icon-wrapper pro">
                        <svg class="section-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="section-title-modern">Nice to Have Developers</h2>
                        <p class="section-subtitle-modern">Nice to have developers with pro profiles</p>
                    </div>
                </div>
            </div>
            <div class="developers-grid-modern">
                @foreach($proDevelopers as $developer)
                    <x-developer-card :developer="$developer" :currentUserDeveloper="$currentUserDeveloper" :recommendedDeveloperIds="$recommendedDeveloperIds" />
                @endforeach
            </div>
        </div>
    @endif

    <!-- Free Developers Section -->
    @if($freeDevelopers->count() > 0)
        <div id="free-developers-section" class="modern-section">
            <div class="section-header-modern">
                <div class="section-title-wrapper">
                    <div class="section-icon-wrapper">
                        <svg class="section-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="section-title-modern">All Developers</h2>
                        <p class="section-subtitle-modern">Browse all registered developers</p>
                    </div>
                </div>
            </div>
            <div class="developers-grid-modern">
                @foreach($freeDevelopers as $developer)
                    <x-developer-card :developer="$developer" :currentUserDeveloper="$currentUserDeveloper" :recommendedDeveloperIds="$recommendedDeveloperIds" :showRecommendedBadge="$developer->is_recommended_by_us" />
                @endforeach
            </div>

            <!-- Modern Pagination -->
            <x-pagination-custom :paginator="$freeDevelopers" />

            <!-- Back to Top Button -->
            <div style="display: flex; justify-content: center; margin-top: var(--spacing-lg, 1.5rem);">
                <a href="#free-developers-section" class="pagination-link" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                    Back to Top
                </a>
            </div>
        </div>
    @endif

    <!-- Modern Empty State -->
    @if($totalCount == 0)
        <div class="empty-state-modern">
            <div class="empty-icon-wrapper">
                <svg class="empty-icon-modern" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <h3 class="empty-title-modern">No developers found</h3>
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
