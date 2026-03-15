<?php

use App\Enums\UserType;
use App\Models\Badge;
use App\Models\Conversation;
use App\Models\Developer;
use App\Models\Message;
use App\Models\User;

beforeEach(function () {
    $this->sender = User::factory()->create([
        'email' => 'ht3aa2001@gmail.com',
        'user_type' => UserType::ADMIN,
    ]);
});

it('fails when sender user does not exist', function () {
    User::query()->where('email', 'ht3aa2001@gmail.com')->delete();

    $this->artisan('developers:send-experience-validation-message')
        ->assertFailed();
});

it('fails when experience-validated badge does not exist', function () {
    Badge::query()->where('slug', 'experience-validated')->delete();

    $this->artisan('developers:send-experience-validation-message')
        ->assertFailed();
});

it('sends message to developers without experience-validated badge', function () {
    $badge = Badge::factory()->create(['slug' => 'experience-validated']);

    $developerWithoutBadge = Developer::factory()->create();
    $developerWithBadge = Developer::factory()->create();
    $developerWithBadge->badges()->attach($badge);

    $this->artisan('developers:send-experience-validation-message')
        ->assertSuccessful();

    $messageBody = 'السلام عليكم شونك استاذ اخبارك.';

    expect(Message::where('body', 'like', $messageBody.'%')->count())->toBe(1);

    $conversation = Conversation::whereHas('participants', fn ($q) => $q->where('user_id', $this->sender->id))
        ->whereHas('participants', fn ($q) => $q->where('user_id', $developerWithoutBadge->user_id))
        ->first();

    expect($conversation)->not->toBeNull();
    expect($conversation->messages()->where('user_id', $this->sender->id)->count())->toBe(1);
});

it('does not send message to developers with experience-validated badge', function () {
    $badge = Badge::factory()->create(['slug' => 'experience-validated']);
    $developerWithBadge = Developer::factory()->create();
    $developerWithBadge->badges()->attach($badge);

    $this->artisan('developers:send-experience-validation-message')
        ->assertSuccessful();

    $messageBody = 'السلام عليكم شونك استاذ اخبارك.';
    expect(Message::where('body', 'like', $messageBody.'%')->count())->toBe(0);
});

it('lists developers in dry-run mode without sending', function () {
    $badge = Badge::factory()->create(['slug' => 'experience-validated']);
    $developer = Developer::factory()->create();

    $this->artisan('developers:send-experience-validation-message', ['--dry-run' => true])
        ->assertSuccessful();

    expect(Message::count())->toBe(0);
});
