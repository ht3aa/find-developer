@props(['developer', 'currentUserDeveloper' => null, 'recommendedDeveloperIds' => []])

<div class="developer-card">
    @if($developer->recommended_by_us)
        <div class="developer-card-recommended-pill developer-card-recommended-pill-standalone">
            <svg class="developer-card-recommended-pill-icon" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            Recommended
        </div>
    @endif
    <!-- Badges at the top -->
    @if($developer->badges->count() > 0)
        <div class="developer-badges-top">
            @foreach($developer->badges as $badge)
                <a href="{{ route('badges') }}" 
                   class="developer-badge-top-item" 
                   x-data="{ showTooltip: false }"
                   @mouseenter="showTooltip = true"
                   @mouseleave="showTooltip = false"
                   style="position: relative;">
                    <div class="badge-icon-wrapper-small" @if($badge->color) style="background: {{ $badge->color }}20; border-color: {{ $badge->color }}40;" @endif>
                                <div class="badge-icon-small" @if($badge->color) style="color: {{ $badge->color }};" @endif>
                                    {!! $badge->getIconHtml() !!}
                                </div>
                    </div>
                    <div x-show="showTooltip" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         x-cloak
                         style="position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 8px; background: @if($badge->color){{ $badge->color }}@else rgba(0, 0, 0, 0.9) @endif; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; white-space: nowrap; pointer-events: none; z-index: 1000;">
                        {{ $badge->name }}
                        <div style="position: absolute; top: 100%; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 6px solid @if($badge->color){{ $badge->color }}@else rgba(0, 0, 0, 0.9) @endif;"></div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <!-- Header -->
    <div class="developer-header">
        <h3 class="developer-name">
            @if($developer->slug)
                <a href="{{ route('developer.profile', $developer->slug) }}" class="developer-name-link">{{ $developer->name }}</a>
            @else
                {{ $developer->name }}
            @endif
        </h3>
        <span class="job-title-badge">
            {{ $developer->jobTitle->name }}
        </span>
    </div>

    <!-- Experience & Availability -->
    <div class="developer-details">
        <div class="detail-item">
            <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            {{ $developer->years_of_experience }} years experience
        </div>

        @if($developer->location)
            <div class="detail-item">
                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ $developer->location->getLabel() }}
            </div>
        @endif

        @if($developer->phone)
            <div class="detail-item">
                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <a href="tel:{{ $developer->phone }}" style="text-decoration: none; color: inherit;">{{ $developer->phone }}</a>
            </div>
        @endif

        @auth
            @if((auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) && ($developer->expected_salary_from || $developer->expected_salary_to))
                <div class="detail-item">
                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    @if($developer->expected_salary_from && $developer->expected_salary_to)
                        {{ number_format($developer->expected_salary_from) }} - {{ number_format($developer->expected_salary_to) }} {{ $developer->currency }}/month
                    @elseif($developer->expected_salary_from)
                        From {{ number_format($developer->expected_salary_from) }} {{ $developer->currency }}/year
                    @else
                        Up to {{ number_format($developer->expected_salary_to) }} {{ $developer->currency }}/year
                    @endif
                </div>
            @elseif(!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin() && ($developer->expected_salary_from || $developer->expected_salary_to))
                <div class="detail-item">
                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <a href="mailto:{{ config('app.contact_email') }}?subject=Subscription%20Inquiry" target="_blank" style="text-decoration: none; color: inherit;">
                        You need to subscribe to see the salary
                    </a>
                </div>
            @endif
        @endauth
        @guest
            @if($developer->expected_salary_from || $developer->expected_salary_to)
                <div class="detail-item">
                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <a href="mailto:{{ config('app.contact_email') }}?subject=Subscription%20Inquiry" target="_blank" style="text-decoration: none; color: inherit;">
                        You need to subscribe to see the salary
                    </a>
                </div>
            @endif
        @endguest

        <div class="detail-item">
            @if($developer->is_available)
                <span class="availability-available">
                    <svg class="detail-icon" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle;">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Available
                </span>
            @else
                <span class="availability-unavailable">
                    <svg class="detail-icon" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle;">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    Not Available
                </span>
            @endif
        </div>

        @if($developer->availability_type && count($developer->availability_type) > 0)
            <div class="detail-item availability-types">
                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="availability-type-badges">
                    @foreach($developer->availability_type as $type)
                        <span class="availability-type-badge availability-type-{{ $type->value }}">
                            {{ $type->getLabel() }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Recommendations Count -->
    @if($developer->recommendations_received_count > 0)
        <div class="detail-item recommendation-count-item">
            <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
            </svg>
            <span class="recommendation-count-badge">{{ $developer->recommendations_received_count }} {{ Str::plural('Recommendation', $developer->recommendations_received_count) }}</span>
        </div>
    @endif

    <!-- Bio for normal -->
    @if($developer->bio)
        <div x-data="{ expanded: false }" class="bio-container">
            @php
                $bioLength = strlen($developer->bio);
                $maxLength = 150;
                $truncatedBio = $bioLength > $maxLength ? substr($developer->bio, 0, $maxLength) . '...' : $developer->bio;
            @endphp
        <p class="developer-bio">
                <span x-show="!expanded">{{ $truncatedBio }}</span>
                <span x-show="expanded" x-cloak>{{ $developer->bio }}</span>
        </p>
            @if($bioLength > $maxLength)
                <button 
                    @click="expanded = !expanded"
                    class="bio-read-more"
                >
                    <span x-show="!expanded">Read more</span>
                    <span x-show="expanded" x-cloak>Show less</span>
                </button>
            @endif
        </div>
    @endif

    <!-- Skills for normal -->
    @if($developer->skills->count() > 0)
        <div x-data="{ expanded: false }" class="skills-container">
            @foreach($developer->skills as $index => $skill)
                <span 
                    class="skill-tag"
                    x-show="expanded || {{ $index }} < 5"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                >
                    {{ $skill->name }}
                </span>
            @endforeach
            @if($developer->skills->count() > 5)
                <span 
                    class="skill-tag skill-tag-more" 
                    @click="expanded = !expanded"
                    style="cursor: pointer;"
                >
                    <span x-show="!expanded">+{{ $developer->skills->count() - 5 }} more</span>
                    <span x-show="expanded" x-cloak>Show less</span>
                </span>
            @endif
        </div>
    @endif


    <!-- Links for normal -->
    <div class="developer-links">
        @if($developer->portfolio_url)
            <a href="{{ $developer->portfolio_url }}" target="_blank" class="social-link"
               x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
               style="position: relative;">
                <svg class="social-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                </svg>
                <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak
                     style="position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 8px; background: rgba(0, 0, 0, 0.9); color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; white-space: nowrap; pointer-events: none; z-index: 1000;">
                    Portfolio
                    <div style="position: absolute; top: 100%; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 6px solid rgba(0, 0, 0, 0.9);"></div>
                </div>
            </a>
        @endif

        @if($developer->github_url)
            <a href="{{ $developer->github_url }}" target="_blank" class="social-link github"
               x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
               style="position: relative;">
                <svg class="social-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                </svg>
                <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak
                     style="position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 8px; background: rgba(0, 0, 0, 0.9); color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; white-space: nowrap; pointer-events: none; z-index: 1000;">
                    GitHub
                    <div style="position: absolute; top: 100%; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 6px solid rgba(0, 0, 0, 0.9);"></div>
                </div>
            </a>
        @endif

        @if($developer->linkedin_url)
            <a href="{{ $developer->linkedin_url }}" target="_blank" class="social-link linkedin"
               x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
               style="position: relative;">
                <svg class="social-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                </svg>
                <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak
                     style="position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 8px; background: rgba(0, 0, 0, 0.9); color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; white-space: nowrap; pointer-events: none; z-index: 1000;">
                    LinkedIn
                    <div style="position: absolute; top: 100%; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 6px solid rgba(0, 0, 0, 0.9);"></div>
                </div>
            </a>
        @endif

        <a href="mailto:{{ $developer->email }}" class="social-link email"
           x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
           style="position: relative;">
            <svg class="social-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak
                 style="position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 8px; background: rgba(0, 0, 0, 0.9); color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; white-space: nowrap; pointer-events: none; z-index: 1000;">
                Email
                <div style="position: absolute; top: 100%; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 6px solid rgba(0, 0, 0, 0.9);"></div>
            </div>
        </a>

        @if($developer->cv_path_url)
            <a href="{{ $developer->cv_path_url }}" target="_blank" rel="noopener noreferrer" class="social-link cv"
               x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
               style="position: relative;">
                <svg class="social-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak
                     style="position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 8px; background: rgba(0, 0, 0, 0.9); color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; white-space: nowrap; pointer-events: none; z-index: 1000;">
                    CV / Resume
                    <div style="position: absolute; top: 100%; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 6px solid rgba(0, 0, 0, 0.9);"></div>
                </div>
            </a>
        @endif

    </div>

    <!-- View Profile & Send Offer Buttons -->
    @if($developer->slug)
    <div class="view-profile-section" style="margin-top: 0.5rem; display: flex; gap: 0.5rem; align-items: center;">
        <a href="{{ route('developer.profile', $developer->slug) }}" class="dev-card-profile-link" style="flex: 1;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            View Full Profile
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
        @auth
        @if(auth()->user()->isHR() || auth()->user()->isSuperAdmin())
        <a href="{{ route('developer.offer', $developer->slug) }}" class="dev-card-offer-link" style="flex: 1;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Send Offer
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
        @else
        <a class="dev-card-offer-link" style="flex: 0.5;" title="Login as HR to send an offer">
            Offer by HR only
        </a>
        @endif
        @endauth
    </div>
    @endif
</div>
