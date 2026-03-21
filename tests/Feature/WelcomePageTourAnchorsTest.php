<?php

test('welcome page and developer directory vue include tour anchors', function () {
    $welcome = file_get_contents(resource_path('js/pages/Welcome.vue'));
    expect($welcome)
        ->toContain('data-tour="welcome-hero"')
        ->toContain('Click me if you need any help');

    $directory = file_get_contents(resource_path('js/components/DeveloperCardSection.vue'));
    expect($directory)
        ->toContain('data-tour="developer-subscribe-cta"')
        ->toContain('data-tour="developer-search"')
        ->toContain('data-tour="developer-filters"')
        ->toContain('data-tour="developer-view-toggle"')
        ->toContain('data-tour="developer-compare"')
        ->toContain('developer-card-badges')
        ->toContain('data-tour="developer-results"');

    $card = file_get_contents(resource_path('js/components/DeveloperCard.vue'));
    expect($card)->toContain('developer-card-badges');

    $tour = file_get_contents(resource_path('js/composables/useWelcomeTour.ts'));
    expect($tour)
        ->toContain('startWelcomeTour')
        ->toContain('driver.js')
        ->toContain('developer-subscribe-cta')
        ->toContain('developer-compare')
        ->toContain('developer-card-badges');
});
