<?php

use Inertia\Testing\AssertableInertia as Assert;

test('about page is accessible to guests', function () {
    $response = $this->get(route('about'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('About/Index'));
});
