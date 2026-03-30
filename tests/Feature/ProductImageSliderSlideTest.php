<?php

test('product image slider uses a sliding track with transform transition', function () {
    $source = file_get_contents(resource_path('js/components/ProductImageSlider.vue'));

    expect($source)
        ->toContain('trackTransform')
        ->toContain('motion-safe:transition-transform')
        ->toContain('translateX');
});

test('product image slider supports autoplay with pause when reduced motion is preferred', function () {
    $source = file_get_contents(resource_path('js/components/ProductImageSlider.vue'));

    expect($source)
        ->toContain('AUTOPLAY_INTERVAL_MS')
        ->toContain('setInterval')
        ->toContain('prefers-reduced-motion');
});
