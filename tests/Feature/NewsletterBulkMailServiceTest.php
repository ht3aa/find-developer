<?php

use App\Models\Newsletter;
use App\Notifications\MailtrapNotification;
use App\Services\NewsletterBulkMailService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

test('send to all notifies each subscriber via mailtrap', function () {
    Notification::fake();

    Newsletter::factory()->create(['email' => 'sub1@example.com']);
    Newsletter::factory()->create(['email' => 'sub2@example.com']);

    $count = app(NewsletterBulkMailService::class)->sendToAll(
        'News Title',
        'Email body content',
        'Newsletter',
    );

    expect($count)->toBe(2);

    Notification::assertSentTo(
        Newsletter::where('email', 'sub1@example.com')->first(),
        MailtrapNotification::class
    );
    Notification::assertSentTo(
        Newsletter::where('email', 'sub2@example.com')->first(),
        MailtrapNotification::class
    );
});

test('send to ids notifies only selected subscribers', function () {
    Notification::fake();

    $a = Newsletter::factory()->create(['email' => 'sub1@example.com']);
    $b = Newsletter::factory()->create(['email' => 'sub2@example.com']);

    $count = app(NewsletterBulkMailService::class)->sendToIds(
        [$a->id, $b->id],
        'News Title',
        'Email body content',
        'Newsletter',
    );

    expect($count)->toBe(2);

    Notification::assertSentTo($a, MailtrapNotification::class);
    Notification::assertSentTo($b, MailtrapNotification::class);
});

test('send to all throws when no subscribers', function () {
    Newsletter::query()->delete();

    expect(fn () => app(NewsletterBulkMailService::class)->sendToAll('T', 'B', null))
        ->toThrow(ValidationException::class);
});

test('send to ids throws when ids array is empty', function () {
    expect(fn () => app(NewsletterBulkMailService::class)->sendToIds([], 'T', 'B', null))
        ->toThrow(ValidationException::class);
});
