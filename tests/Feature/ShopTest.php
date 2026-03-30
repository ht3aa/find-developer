<?php

use Inertia\Testing\AssertableInertia as Assert;

test('shop page renders for guests', function () {
    $response = $this->get(route('shop'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Shop'));
});

test('shop product cards document free delivery in the page source', function () {
    $shop = file_get_contents(resource_path('js/pages/Shop.vue'));

    expect($shop)->toContain('Free delivery.');
});
