<?php

use Inertia\Testing\AssertableInertia as Assert;

test('shop page renders for guests', function () {
    $response = $this->get(route('shop'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Shop'));
});
