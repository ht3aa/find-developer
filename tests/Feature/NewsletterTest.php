<?php

use App\Models\Newsletter;

test('guest can subscribe to newsletter with valid email', function () {
    $response = $this->post(route('newsletter.store'), [
        'email' => 'subscriber@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect(Newsletter::where('email', 'subscriber@example.com')->exists())->toBeTrue();
});

test('newsletter subscription rejects duplicate email', function () {
    Newsletter::factory()->create(['email' => 'existing@example.com']);

    $response = $this->post(route('newsletter.store'), [
        'email' => 'existing@example.com',
    ]);

    $response->assertSessionHasErrors('email');
    expect(Newsletter::count())->toBe(1);
});

test('newsletter subscription validates email format', function () {
    $response = $this->post(route('newsletter.store'), [
        'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors('email');
    expect(Newsletter::count())->toBe(0);
});
