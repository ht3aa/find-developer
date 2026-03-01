<?php

use App\Models\Newsletter;
use App\Models\User;
use App\Notifications\MailtrapNotification;
use Illuminate\Support\Facades\Notification;

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

test('dashboard newsletter bulk-email-all redirects guest to login', function () {
    $response = $this->post(route('dashboard.newsletter.bulk-email-all'), [
        'title' => 'Test',
        'body' => 'Body',
    ]);
    $response->assertRedirect(route('login'));
});

test('dashboard newsletter bulk-email-all returns 403 for non super admin', function () {
    $user = User::factory()->create(['email' => 'nonsuper@example.com']);
    $this->actingAs($user);

    $response = $this->post(route('dashboard.newsletter.bulk-email-all'), [
        'title' => 'Test',
        'body' => 'Body',
    ]);
    $response->assertForbidden();
});

test('super admin can send bulk email to all newsletter subscribers', function () {
    Notification::fake();
    $superEmail = 'superadmin@example.com';
    config(['app.super_admin_emails' => $superEmail]);
    $user = User::factory()->create(['email' => $superEmail]);
    $this->actingAs($user);
    Newsletter::factory()->create(['email' => 'sub1@example.com']);
    Newsletter::factory()->create(['email' => 'sub2@example.com']);

    $response = $this->post(route('dashboard.newsletter.bulk-email-all'), [
        'title' => 'News Title',
        'body' => 'Email body content',
        'category' => 'Newsletter',
    ]);

    $response->assertRedirect(route('dashboard.newsletter.index'));
    $response->assertSessionHas('success');
    Notification::assertSentTo(
        Newsletter::where('email', 'sub1@example.com')->first(),
        MailtrapNotification::class
    );
    Notification::assertSentTo(
        Newsletter::where('email', 'sub2@example.com')->first(),
        MailtrapNotification::class
    );
});

test('dashboard newsletter bulk-email redirects guest to login', function () {
    $response = $this->post(route('dashboard.newsletter.bulk-email'), [
        'subscriber_ids' => [1],
        'title' => 'Test',
        'body' => 'Body',
    ]);
    $response->assertRedirect(route('login'));
});

test('dashboard newsletter bulk-email returns 403 for non super admin', function () {
    $user = User::factory()->create(['email' => 'nonsuper@example.com']);
    $this->actingAs($user);

    $response = $this->post(route('dashboard.newsletter.bulk-email'), [
        'subscriber_ids' => [1],
        'title' => 'Test',
        'body' => 'Body',
    ]);
    $response->assertForbidden();
});

test('super admin can send bulk email to selected newsletter subscribers', function () {
    Notification::fake();
    $superEmail = 'superadmin@example.com';
    config(['app.super_admin_emails' => $superEmail]);
    $user = User::factory()->create(['email' => $superEmail]);
    $a = Newsletter::factory()->create(['email' => 'sub1@example.com']);
    $b = Newsletter::factory()->create(['email' => 'sub2@example.com']);

    $response = $this->actingAs($user)->post(route('dashboard.newsletter.bulk-email'), [
        'subscriber_ids' => [$a->id, $b->id],
        'title' => 'News Title',
        'body' => 'Email body content',
        'category' => 'Newsletter',
    ]);

    $response->assertRedirect(route('dashboard.newsletter.index'));
    $response->assertSessionHas('success');
    Notification::assertSentTo($a, MailtrapNotification::class);
    Notification::assertSentTo($b, MailtrapNotification::class);
});
