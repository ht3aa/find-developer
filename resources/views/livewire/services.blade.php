<div class="services-container">
    <div class="services-header">
        <h1 class="services-title">
            <svg class="services-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
            </svg>
            Our Services
        </h1>
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
                                    LinkedIn
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="service-list">
                        @foreach($userServices as $service)
                            <div class="service-card">
                                <x-developer-badges-icons :badges="$service->badges" />

                                <div class="service-card-header">
                                    <h4 class="service-name">{{ $service->name }}</h4>
                                    @if($service->is_active)
                                        <span class="service-status service-status-active">Active</span>
                                    @else
                                        <span class="service-status service-status-inactive">Inactive</span>
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
                                        >
                                            {!! $descFull !!}
                                        </div>
                                        @if($descLong)
                                            <button @click="expanded = !expanded" class="service-read-more">
                                                <span x-show="!expanded">Read more</span>
                                                <span x-show="expanded" x-cloak>Show less</span>
                                            </button>
                                        @endif
                                    </div>
                                @endif

                                <div class="service-meta">
                                    @if($service->price)
                                        <div class="service-meta-item">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/>
                                            </svg>
                                            <span>{{ number_format($service->price) }} {{ $service->price_currency->value }}</span>
                                        </div>
                                    @endif

                                    @if($service->time_minutes)
                                        <div class="service-meta-item">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            <span>{{ $service->time_minutes }} min</span>
                                        </div>
                                    @endif

                                    <div class="service-meta-item">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>
                                        </svg>
                                        <span>{{ $service->appointments_count ?? 0 }} booked</span>
                                    </div>
                                </div>

                                <a href="mailto:ht3aa2001@gmail.com?subject={{ urlencode('Service Inquiry: ' . $service->name) }}&body={{ urlencode('Hello, I would like to inquire about ' . $service->name . ' service. For user: ' . $user->name) }}" target="_blank" rel="noopener noreferrer" class="service-contact-btn">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                    </svg>
                                    Contact Us
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
