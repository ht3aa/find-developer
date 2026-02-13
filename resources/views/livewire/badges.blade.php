<div class="badges-container">
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
            @foreach($badges as $badge)
                <div class="badge-card">
                    <div class="badge-card-header">
                        <div class="badge-icon-wrapper" @if($badge->color) style="background: {{ $badge->color }}20; border-color: {{ $badge->color }}40;" @endif>
                                <div class="badge-icon-large" @if($badge->color) style="color: {{ $badge->color }};" @endif>
                                    {!! $badge->getIconHtml() !!}
                                </div>
                        </div>
                        <div class="badge-header-content">
                            <h2 class="badge-name" @if($badge->color) style="color: {{ $badge->color }}; background: none; -webkit-background-clip: unset; -webkit-text-fill-color: {{ $badge->color }}; background-clip: unset;" @endif>{{ $badge->name }}</h2>
                        </div>
                    </div>
                    <div class="badge-card-body">
                        @php
                            $descriptionData = $this->getTruncatedDescription($badge);
                        @endphp
                        @if($descriptionData['text'])
                            <div class="badge-description" x-data="{ expanded: false }">
                                <h3 class="badge-description-title">Benefits:</h3>
                                <p class="badge-description-text">
                                    <span x-show="!expanded">{{ $descriptionData['text'] }}</span>
                                    <span x-show="expanded" x-cloak>{{ $descriptionData['fullText'] }}</span>
                                </p>
                                @if($descriptionData['isTruncated'])
                                    <button 
                                        @click="expanded = !expanded"
                                        class="badge-description-read-more"
                                    >
                                        <span x-show="!expanded">Read more</span>
                                        <span x-show="expanded" x-cloak>Show less</span>
                                    </button>
                                @endif
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
    @endif
</div>
