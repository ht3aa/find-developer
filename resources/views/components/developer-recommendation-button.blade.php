@props(['developer', 'currentUserDeveloper' => null, 'recommendedDeveloperIds' => null])

<!-- Developer Recommendation Button -->
@auth
    @php
        $recommender = $currentUserDeveloper ?? (auth()->user()->isDeveloper() ? auth()->user()->developer : null);
        $isOwnProfile = $recommender && $recommender->id === $developer->id;
        $hasPendingOrApprovedRecommendation = $recommender && (
            $recommendedDeveloperIds !== null
                ? in_array($developer->id, $recommendedDeveloperIds)
                : \App\Models\DeveloperRecommendation::where('recommender_id', $recommender->id)
                    ->where('recommended_id', $developer->id)
                    ->whereIn('status', [\App\Enums\RecommendationStatus::PENDING, \App\Enums\RecommendationStatus::APPROVED])
                    ->exists()
        );
    @endphp
    @if($recommender)
        <div class="developer-recommendation-section">
            @if($isOwnProfile)
                <button 
                    disabled
                    class="recommend-button"
                    title="You cannot recommend yourself"
                >
                    <svg class="recommend-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span>Cannot Recommend Yourself</span>
                </button>
            @elseif($hasPendingOrApprovedRecommendation)
                <div class="recommend-button recommended">
                    <svg class="recommend-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span>Recommended</span>
                </div>
            @else
                <a 
                    href="{{ route('developer.recommend', $developer->id) }}"
                    class="recommend-button"
                    title="Recommend this developer"
                >
                    <svg class="recommend-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span>Recommend</span>
                </a>
            @endif
        </div>
    @endif
@else
    <div class="developer-recommendation-section">
        <a href="{{ route('filament.admin.auth.login') }}" class="recommend-button recommend-login">
            <svg class="recommend-icon" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            <span>Login to Recommend</span>
        </a>
    </div>
@endauth

<style>
.developer-recommendation-section {
    margin-top: var(--spacing-md);
    padding-top: var(--spacing-md);
    border-top: 1px solid var(--border-primary);
}

.recommend-button {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm) var(--spacing-md);
    background: var(--bg-primary);
    border: 1px solid var(--border-primary);
    border-radius: var(--radius-md);
    color: var(--text-secondary);
    font-size: var(--font-size-sm);
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
}

.recommend-button:hover:not(:disabled) {
    background: var(--bg-tertiary);
    border-color: var(--color-primary);
    color: var(--color-primary);
}

.recommend-button.recommended {
    background: rgba(59, 130, 246, 0.1);
    border-color: var(--color-primary);
    color: var(--color-primary);
}

.recommend-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.recommend-button.loading {
    cursor: wait;
}

.recommend-icon {
    width: 1rem;
    height: 1rem;
    flex-shrink: 0;
}

.loading-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.recommend-login {
    border-style: dashed;
}
</style>
