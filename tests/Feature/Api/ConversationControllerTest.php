<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('returns cursor-paginated conversations for authenticated user', function () {
    for ($i = 0; $i < 20; $i++) {
        [$conversation] = Conversation::findOrCreateBetween(
            $this->user->id,
            User::factory()->create()->id,
        );
        $message = Message::factory()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $this->user->id,
        ]);
        $conversation->update(['last_message_id' => $message->id]);
    }

    $response = $this->actingAs($this->user)->getJson('/api/conversations');

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'participant',
                'last_message',
                'unread_count',
                'updated_at',
            ],
        ],
        'meta' => [
            'has_more',
        ],
    ]);
    expect(count($response->json('data')))->toBe(15);
    expect($response->json('meta.has_more'))->toBeTrue();
});

it('returns older conversations when before_id is passed', function () {
    for ($i = 0; $i < 20; $i++) {
        [$conversation] = Conversation::findOrCreateBetween(
            $this->user->id,
            User::factory()->create()->id,
        );
        $message = Message::factory()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $this->user->id,
        ]);
        $conversation->update(['last_message_id' => $message->id]);
    }

    $first = $this->actingAs($this->user)->getJson('/api/conversations');
    $lastItem = $first->json('data.14');
    $beforeId = $lastItem['id'];

    $response = $this->actingAs($this->user)->getJson('/api/conversations?before_id='.$beforeId);

    $response->assertSuccessful();
    expect(count($response->json('data')))->toBe(5);
    expect($response->json('meta.has_more'))->toBeFalse();
});

it('requires authentication', function () {
    $response = $this->getJson('/api/conversations');

    $response->assertUnauthorized();
});
