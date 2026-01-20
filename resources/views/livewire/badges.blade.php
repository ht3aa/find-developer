<div class="badges-container" x-data="{ showAll: false, initialCount: 9 }">
    <div class="badges-header">
        <h1 class="badges-title">Developer Badges</h1>
        <p class="badges-subtitle">Earn badges to showcase your achievements and stand out to potential clients</p>
    </div>

    @if($badges->isEmpty())
        <div class="badges-empty">
            <svg class="badges-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <h3 class="badges-empty-title">No badges available</h3>
            <p class="badges-empty-text">Check back later for available badges.</p>
        </div>
    @else
        <div class="badges-grid">
            @foreach($badges as $index => $badge)
                <div 
                    class="badge-card"
                    x-show="showAll || {{ $index }} < initialCount"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                >
                    <div class="badge-card-header">
                        <div class="badge-icon-wrapper" @if($badge->color) style="background: {{ $badge->color }}20; border-color: {{ $badge->color }}40;" @endif>
                            <svg class="badge-icon-large" fill="none" stroke="currentColor" viewBox="0 0 24 24" @if($badge->color) style="color: {{ $badge->color }};" @endif>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <div class="badge-header-content">
                            <h2 class="badge-name" @if($badge->color) style="color: {{ $badge->color }}; background: none; -webkit-background-clip: unset; -webkit-text-fill-color: {{ $badge->color }}; background-clip: unset;" @endif>{{ $badge->name }}</h2>
                        </div>
                    </div>
                    <div class="badge-card-body">
                        @if($badge->description)
                            <div class="badge-description">
                                <h3 class="badge-description-title">Benefits:</h3>
                                <p class="badge-description-text">{{ $badge->description }}</p>
                            </div>
                        @else
                            <div class="badge-description">
                                <p class="badge-description-text">No description available for this badge.</p>
                            </div>
                        @endif
                    </div>
                    <div class="badge-card-footer">
                        <span class="badge-status">Available to earn</span>
                    </div>
                </div>
            @endforeach
        </div>

        @if($badges->count() > 9)
            <div class="badges-show-more-container">
                <button 
                    @click="showAll = !showAll"
                    class="badges-show-more-btn"
                >
                    <span x-show="!showAll">Show More Badges ({{ $badges->count() - 9 }} more)</span>
                    <span x-show="showAll" x-cloak>Show Less</span>
                    <svg 
                        class="badges-show-more-icon" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                        :class="{ 'rotate-180': showAll }"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
        @endif
    @endif
</div>
