<?php

use App\Enums\HackathonSubscriberStatus;
use App\Models\Developer;
use App\Models\Hackathon;
use App\Models\HackathonSubscriber;
use App\Notifications\HackathonSubscriberConfirmedNotification;
use Illuminate\Support\Facades\Notification;

test('sends confirmation email to developer when hackathon subscriber status is changed to confirmed', function () {
    Notification::fake();

    $hackathon = Hackathon::factory()->create(['title' => 'Test Hackathon']);
    $developer = Developer::factory()->create(['name' => 'Jane Dev']);
    $subscriber = HackathonSubscriber::create([
        'hackathon_id' => $hackathon->id,
        'developer_id' => $developer->id,
        'message' => 'I want to join',
        'status' => HackathonSubscriberStatus::Pending,
    ]);

    $subscriber->update(['status' => HackathonSubscriberStatus::Confirmed]);

    Notification::assertSentTo($developer, HackathonSubscriberConfirmedNotification::class);
});

test('does not send confirmation email when status is changed to something other than confirmed', function () {
    Notification::fake();

    $hackathon = Hackathon::factory()->create();
    $developer = Developer::factory()->create();
    $subscriber = HackathonSubscriber::create([
        'hackathon_id' => $hackathon->id,
        'developer_id' => $developer->id,
        'message' => 'I want to join',
        'status' => HackathonSubscriberStatus::Pending,
    ]);

    $subscriber->update(['status' => HackathonSubscriberStatus::Cancelled]);

    Notification::assertNotSentTo($developer, HackathonSubscriberConfirmedNotification::class);
});

test('does not send confirmation email when status is unchanged', function () {
    Notification::fake();

    $hackathon = Hackathon::factory()->create();
    $developer = Developer::factory()->create();
    $subscriber = HackathonSubscriber::create([
        'hackathon_id' => $hackathon->id,
        'developer_id' => $developer->id,
        'message' => 'I want to join',
        'status' => HackathonSubscriberStatus::Confirmed,
    ]);

    $subscriber->update(['message' => 'Updated message']);

    Notification::assertNothingSent();
});
