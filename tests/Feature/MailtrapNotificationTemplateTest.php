<?php

use App\Models\User;
use App\Notifications\MailtrapBulkNotification;
use App\Notifications\MailtrapNotification;

it('renders mailtrap notification html with primary amber branding', function () {
    $user = User::factory()->create();
    $notification = new MailtrapNotification('Weekly update', "Hello\nThere");
    $message = $notification->toMailtrap($user);

    expect($message->html)
        ->toContain('hsl(38, 92%, 50%)')
        ->toContain('Weekly update')
        ->toContain('Hello')
        ->not->toContain('rgb(0, 49, 173)');
});

it('renders mailtrap bulk notification html with primary amber branding', function () {
    $user = User::factory()->create();
    $notification = new MailtrapBulkNotification(['a@example.com'], 'Bulk subject', "Line one\nLine two");
    $message = $notification->toMailtrapBulk($user);

    expect($message->html)
        ->toContain('hsl(38, 92%, 50%)')
        ->toContain('Bulk subject')
        ->toContain('Line one')
        ->not->toContain('rgb(0, 49, 173)');
});
