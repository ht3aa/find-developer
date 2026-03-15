<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Conversation $conversation) {
            if ($conversation->participants()->count() === 0) {
                $conversation->participants()->attach([
                    User::factory()->create()->id,
                    User::factory()->create()->id,
                ]);
            }
        });
    }

    /**
     * Use specific users as participants instead of creating new ones.
     */
    public function forUsers(User $userA, User $userB): static
    {
        return $this->state([])->afterCreating(function (Conversation $conversation) use ($userA, $userB) {
            $conversation->participants()->sync([$userA->id, $userB->id]);
        });
    }

    /**
     * Add a last message to the conversation (required for API listing).
     */
    public function withLastMessage(?User $sender = null): static
    {
        return $this->afterCreating(function (Conversation $conversation) use ($sender) {
            $messageUser = $sender ?? $conversation->participants()->first() ?? User::factory()->create();

            $message = Message::factory()->create([
                'conversation_id' => $conversation->id,
                'user_id' => $messageUser->id,
            ]);

            $conversation->update(['last_message_id' => $message->id]);
        });
    }
}
