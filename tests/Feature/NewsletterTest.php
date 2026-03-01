<?php

use App\Models\Newsletter;
use App\Models\User;

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

test('dashboard newsletter index redirects guest to login', function () {
    $response = $this->get(route('dashboard.newsletter.index'));
    $response->assertRedirect(route('login'));
});

test('dashboard newsletter index returns 403 for non super admin', function () {
    $user = User::factory()->create(['email' => 'nonsuper@example.com']);
    $this->actingAs($user);

    $response = $this->get(route('dashboard.newsletter.index'));
    $response->assertForbidden();
});

test('super admin can view dashboard newsletter index', function () {
    $superEmail = 'superadmin@example.com';
    config(['app.super_admin_emails' => $superEmail]);
    $user = User::factory()->create(['email' => $superEmail]);
    $this->actingAs($user);

    $response = $this->get(route('dashboard.newsletter.index'));
    $response->assertOk();
});
