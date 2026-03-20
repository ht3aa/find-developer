<?php

use Inertia\Testing\AssertableInertia as Assert;

test('privacy policy page is accessible to guests', function () {
    $response = $this->get(route('privacy-policy'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Legal/PrivacyPolicy'));
});
