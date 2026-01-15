<div class="container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Find Your Perfect Developer</h1>
        <p class="page-subtitle">Search from our pool of talented developers</p>
    </div>

    <!-- Search & Filter Section -->
    <div class="search-card">
        <div class="search-grid">
            <!-- Search Input -->
            <div class="form-group">
                <label for="search" class="form-label">Search</label>
                <input 
                    type="text" 
                    id="search"
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Name, email, location, skills..."
                    class="form-input"
                >
            </div>

            <!-- Job Title Filter -->
            <div class="form-group">
                <label for="jobTitle" class="form-label">Job Titles</label>
                <select 
                    id="jobTitle"
                    wire:model.live="jobTitleIds"
                    class="form-select"
                    multiple
                    size="1"
                >
                    @foreach($jobTitles as $jobTitle)
                        <option value="{{ $jobTitle->id }}">{{ $jobTitle->name }}</option>
                    @endforeach
                </select>
                @if(!empty($jobTitleIds))
                    <div class="selected-filters">
                        @foreach($jobTitles->whereIn('id', $jobTitleIds) as $jobTitle)
                            <span class="filter-badge">
                                {{ $jobTitle->name }}
                                <button 
                                    type="button" 
                                    wire:click="$set('jobTitleIds', {{ json_encode(array_values(array_diff($jobTitleIds, [$jobTitle->id]))) }})"
                                    class="filter-remove"
                                >×</button>
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Skills Filter -->
            <div class="form-group">
                <label for="skills" class="form-label">Skills</label>
                <select 
                    id="skills"
                    wire:model.live="skillIds"
                    class="form-select"
                    multiple
                    size="1"
                >
                    @foreach($skills as $skill)
                        <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                    @endforeach
                </select>
                @if(!empty($skillIds))
                    <div class="selected-filters">
                        @foreach($skills->whereIn('id', $skillIds) as $skill)
                            <span class="filter-badge">
                                {{ $skill->name }}
                                <button 
                                    type="button" 
                                    wire:click="$set('skillIds', {{ json_encode(array_values(array_diff($skillIds, [$skill->id]))) }})"
                                    class="filter-remove"
                                >×</button>
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Min Experience -->
            <div class="form-group">
                <label for="minExperience" class="form-label">Min Experience</label>
                <input 
                    type="number" 
                    id="minExperience"
                    wire:model.live.debounce.300ms="minExperience" 
                    placeholder="0"
                    min="0"
                    class="form-input"
                >
            </div>

            <!-- Max Experience -->
            <div class="form-group">
                <label for="maxExperience" class="form-label">Max Experience</label>
                <input 
                    type="number" 
                    id="maxExperience"
                    wire:model.live.debounce.300ms="maxExperience" 
                    placeholder="50"
                    min="0"
                    class="form-input"
                >
            </div>
        </div>

        <!-- Available Only & Clear Filters -->
        <div class="search-footer">
            <label class="checkbox-label">
                <input 
                    type="checkbox" 
                    wire:model.live="availableOnly"
                    class="checkbox-input"
                >
                <span class="checkbox-text">Available only</span>
            </label>

            <button 
                wire:click="clearFilters"
                class="btn-link"
            >
                Clear all filters
            </button>
        </div>
    </div>

    <!-- Results Count -->
    <div class="results-count">
        Found <span class="results-count-number">{{ $totalCount }}</span> developer(s)
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="loading-indicator">
        Loading...
    </div>

    <!-- Premium Developers Section -->
    @if($premiumDevelopers->count() > 0)
        <div class="section-header">
            <h2 class="section-title">
                <svg class="section-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Premium Developers
            </h2>
            <p class="section-subtitle">Featured developers with premium profiles</p>
        </div>
        <div class="developers-grid">
            @foreach($premiumDevelopers as $developer)
                <x-developer-card :developer="$developer" />
            @endforeach
        </div>
    @endif

    <!-- Pro Developers Section -->
    @if($proDevelopers->count() > 0)
        <div class="section-header">
            <h2 class="section-title">
                <svg class="section-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Pro Developers
            </h2>
            <p class="section-subtitle">Verified professionals with pro accounts</p>
        </div>
        <div class="developers-grid">
            @foreach($proDevelopers as $developer)
                <x-developer-card :developer="$developer" />
            @endforeach
        </div>
    @endif

    <!-- Free Developers Section -->
    @if($freeDevelopers->count() > 0)
        <div class="section-header">
            <h2 class="section-title">
                <svg class="section-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                All Developers
            </h2>
            <p class="section-subtitle">Browse all registered developers</p>
        </div>
        <div class="developers-grid">
            @foreach($freeDevelopers as $developer)
                <x-developer-card :developer="$developer" />
            @endforeach
        </div>

        <!-- Pagination (only for free developers) -->
        <div class="pagination-container">
            {{ $freeDevelopers->links() }}
        </div>
    @endif

    <!-- Empty State -->
    @if($totalCount == 0)
        <div class="empty-state">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 class="empty-title">No developers found</h3>
            <p class="empty-text">Try adjusting your search criteria or clearing filters.</p>
        </div>
    @endif
</div>
