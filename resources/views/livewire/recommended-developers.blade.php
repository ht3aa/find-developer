<div class="recommended-container">
    <!-- Header -->
    <div class="recommended-header">
        <div class="recommended-header-content">
            <h1 class="recommended-title">
                <svg class="recommended-title-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Recommended By Us
            </h1>
            <p class="recommended-subtitle">Handpicked top-tier developers ready to bring your projects to life</p>
        </div>
    </div>

    <!-- Recommended Developers Grid -->
    <div class="recommended-developers-section">
        @if($developers->count() > 0)
            <div class="recommended-order-info">
                <svg class="order-info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="order-info-text">Results are ordered by number of recommendations</span>
            </div>
            <div class="recommended-grid">
                @foreach($developers as $developer)
                    <x-developer-recommended-card :developer="$developer" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="recommended-pagination">
                {{ $developers->links() }}
            </div>
        @else
            <div class="recommended-empty">
                <svg class="recommended-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="recommended-empty-text">No recommended developers at the moment. Check back soon!</p>
            </div>
        @endif
    </div>
</div>
