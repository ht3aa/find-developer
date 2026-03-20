<?php

use Inertia\Testing\AssertableInertia as Assert;

test('home page passes hero greeting note to welcome', function () {
    $response = $this->get('/');

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->where(
            'heroGreetingNote',
            'أيامكم سعيدة، وينعاد عليكم بالصحة والعافية، وإن شاء الله المنصة تكبر بوجودكم وتفيدكم أكثر فأكثر.',
        )
    );
});
