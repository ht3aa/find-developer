<?php

test('welcome page and developer directory vue include tour anchors', function () {
    $welcome = file_get_contents(resource_path('js/pages/Welcome.vue'));
    expect($welcome)
        ->toContain('data-tour="welcome-hero"')
        ->toContain('Click me if you need any help')
        ->toContain('surah110TitleAr')
        ->toContain('surah110Ayahs')
        ->toContain('NewsletterSignup');

    $newsletterSignup = file_get_contents(resource_path('js/components/NewsletterSignup.vue'));
    expect($newsletterSignup)->toContain('Get developers spotlight in your inbox');

    $directory = file_get_contents(resource_path('js/components/DeveloperCardSection.vue'));
    expect($directory)
        ->toContain('data-tour="developer-subscribe-cta"')
        ->toContain('developer-card-badges')
        ->toContain('data-tour="developer-results"')
        ->toContain('startMessageToDeveloper')
        ->toContain('canMessageDeveloper')
        ->toContain('developerBadgesPageUrl');

    $filtersSidebar = file_get_contents(resource_path('js/components/DeveloperFiltersSidebar.vue'));
    expect($filtersSidebar)
        ->toContain('data-tour="developer-filters"')
        ->toContain('data-tour="developer-search"')
        ->toContain('data-tour="developer-view-toggle"')
        ->toContain('data-tour="developer-compare"');

    $card = file_get_contents(resource_path('js/components/DeveloperCard.vue'));
    expect($card)
        ->toContain('developer-card-badges')
        ->toContain('startConversation')
        ->toContain('MessageSquare')
        ->not->toContain('hover:-translate-y-1')
        ->not->toContain('hover:scale-105')
        ->not->toContain('hover:scale-110');

    $tour = file_get_contents(resource_path('js/composables/useWelcomeTour.ts'));
    expect($tour)
        ->toContain('startWelcomeTour')
        ->toContain('driver.js')
        ->toContain('developer-subscribe-cta')
        ->toContain('developer-compare')
        ->toContain('developer-card-badges');
});
