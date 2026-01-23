<div class="services-container">
    <div class="services-header">
        <h1 class="services-title">Our Services</h1>
        <p class="services-subtitle">Professional development services offered by our partners</p>
    </div>

    @if($services->isEmpty())
        <div class="services-empty">
            <svg class="services-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="services-empty-title">No services available</h3>
            <p class="services-empty-text">Check back later for available services.</p>
        </div>
    @else
        <div class="services-grid">
            @foreach($services as $userId => $userServices)
                @php
                    $user = $userServices->first()->user;
                @endphp
                <div class="service-provider-card">
                    <div class="service-provider-header">
                        <div class="service-provider-avatar">
                            <span class="service-provider-initials">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                        <div class="service-provider-info">
                            <h2 class="service-provider-name">{{ $user->name }}</h2>
                            @if($user->linkedin_url)
                                <a href="{{ $user->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="service-provider-linkedin">
                                    <svg class="service-provider-linkedin-icon" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                    <span>LinkedIn Profile</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="service-provider-services">
                        <h3 class="service-provider-services-title">Available Services</h3>
                        <div class="service-list">
                            @foreach($userServices as $service)
                                <div class="service-card">
                                    <!-- Badges at the top -->
                                    @if($service->badges->isNotEmpty())
                                        <div class="service-badges-top">
                                            @foreach($service->badges as $badge)
                                                <a href="{{ route('badges') }}" class="service-badge-top-item">
                                                    <div class="badge-icon-wrapper-small" @if($badge->color) style="background: {{ $badge->color }}20; border-color: {{ $badge->color }}40;" @endif>
                                                        <svg class="badge-icon-small" fill="none" stroke="currentColor" viewBox="0 0 24 24" @if($badge->color) style="color: {{ $badge->color }};" @endif>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="service-badge-name-small" @if($badge->color) style="color: {{ $badge->color }};" @endif>{{ $badge->name }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="service-card-header">
                                        <h4 class="service-name">{{ $service->name }}</h4>
                                        @if($service->is_active)
                                            <span class="service-badge service-badge-active">Active</span>
                                        @else
                                            <span class="service-badge service-badge-inactive">Inactive</span>
                                        @endif
                                    </div>

                                    @if($service->description)
                                        @php
                                            $descFull = $service->description;
                                            $descPlain = strip_tags($descFull);
                                            $descLong = strlen($descPlain) > 150;
                                        @endphp
                                        <div x-data="{ expanded: false }" class="service-description-container">
                                            <div 
                                                class="service-description"
                                                :class="{ 'service-description-collapsed': !expanded }"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                            >
                                                {!! $descFull !!}
                                            </div>
                                            @if($descLong)
                                                <button 
                                                    @click="expanded = !expanded"
                                                    class="service-description-read-more"
                                                >
                                                    <span x-show="!expanded">Read more</span>
                                                    <span x-show="expanded" x-cloak>Show less</span>
                                                </button>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="service-details">
                                        @if($service->price)
                                            <div class="service-detail-item">
                                                <svg class="service-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="service-detail-label">Price:</span>
                                                <span class="service-detail-value">{{ number_format($service->price) }} {{ $service->price_currency->value }}</span>
                                            </div>
                                        @endif

                                        @if($service->time_minutes)
                                            <div class="service-detail-item">
                                                <svg class="service-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="service-detail-label">Duration:</span>
                                                <span class="service-detail-value">{{ $service->time_minutes }} minutes</span>
                                            </div>
                                        @endif

                                        <div class="service-detail-item">
                                            <svg class="service-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="service-detail-label">Appointments:</span>
                                            <span class="service-detail-value">{{ $service->appointments_count ?? 0 }}</span>
                                        </div>
                                    </div>

                                    <div class="service-card-footer">
                                        <a href="mailto:ht3aa2001@gmail.com?subject={{ urlencode('Service Inquiry: ' . $service->name) }}&body={{ urlencode('Hello, I would like to inquire about ' . $service->name . ' service. For user: ' . $user->name) }}" target="_blank" rel="noopener noreferrer" class="service-cta-button">
                                            <svg class="service-cta-icon" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                            </svg>
                                            <span>Contact Us</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
