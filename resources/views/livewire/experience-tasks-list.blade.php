<div class="modern-container">
    <!-- Header -->
    <div class="modern-header">
        <div class="header-content">
            <h1 class="modern-title">Get Experience</h1>
            <p class="modern-subtitle">Small tasks to build your experience and earn XP.</p>
            <p class="modern-subtitle-note">Note: the price of the tasks is split between the developers.</p>
        </div>
    </div>

    <!-- Search Bar -->
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
                placeholder="Search by title or description..."
                class="search-input"
            />
        </div>
    </div>

    <!-- Results Count -->
    <div class="results-header">
        <div class="results-count-modern">
            <span class="results-label">Found</span>
            <span class="results-number">{{ $totalCount }}</span>
            <span class="results-label">task{{ $totalCount !== 1 ? 's' : '' }}</span>
        </div>
    </div>

    <!-- Loading -->
    <div wire:loading class="modern-loading">
        <div class="loading-spinner"></div>
        <span class="loading-text">Loading tasks...</span>
    </div>

    <!-- Tasks Grid -->
    @if($tasks->count() > 0)
        <div class="xp-tasks-grid">
            @foreach($tasks as $task)
                <article class="xp-task-card">
                    <div class="xp-task-card-header">
                        <h2 class="xp-task-card-title">{{ $task->title }}</h2>
                        <span class="xp-task-badge xp-task-badge-{{ $task->status->value }}">{{ $task->status->getLabel() }}</span>
                    </div>

                    <div class="xp-task-card-body">
                        <div class="xp-task-info-row">
                            @if($task->experience_gain?->value > 0)
                                <div class="xp-task-info-item">
                                    <svg class="xp-task-info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span>{{ $task->experience_gain->getLabel() }} XP per developer</span>
                                </div>
                            @endif

                            @if($task->price)
                                <div class="xp-task-info-item">
                                    <svg class="xp-task-info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ number_format($task->price) }} {{ $task->price_currency?->value ?? 'IQD' }}</span>
                                </div>
                            @endif

                            <div class="xp-task-info-item">
                                <svg class="xp-task-info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>{{ $task->developers_count }} / {{ $task->required_developers_count }} assigned</span>
                            </div>
                        </div>

                        <div class="xp-task-description">
                            {{ \Illuminate\Support\Str::limit(strip_tags($task->description), 200) }}
                        </div>
                    </div>

                    <div class="xp-task-card-footer">
                        <a
                            href="mailto:ht3aa2001@gmail.com?subject=Get%20Experience%3A%20{{ urlencode($task->title) }}&body=Hello%2C%20I%20would%20like%20to%20express%20interest%20in%20the%20task%3A%20{{ urlencode($task->title) }}"
                            class="xp-task-email-btn"
                        >
                            <svg class="xp-task-email-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Email Us
                        </a>
                        <span class="xp-task-date">Posted {{ $task->created_at->diffForHumans() }}</span>
                    </div>
                </article>
            @endforeach
        </div>

        <x-pagination-custom :paginator="$tasks" />
    @endif

    <!-- Empty State -->
    @if($totalCount == 0)
        <div class="empty-state-modern">
            <div class="empty-icon-wrapper">
                <svg class="empty-icon-modern" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="empty-title-modern">No tasks found</h3>
            <p class="empty-text-modern">Try adjusting your search. New tasks are added regularly.</p>
            <button type="button" wire:click="clearFilters" class="empty-action-btn">
                Clear search
            </button>
        </div>
    @endif
</div>
