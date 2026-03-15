<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MoreConversationsSeeder extends Seeder
{
    /**
     * Add more conversations for ht3aa2001@gmail.com.
     */
    public function run(): void
    {
        $user = User::where('email', 'ht3aa2001@gmail.com')->first();
        if (! $user) {
            $this->command->warn('User (ht3aa2001@gmail.com) not found.');

            return;
        }

        $otherUsers = User::factory(25)->create();

        $bodies = [
            'Hi! Interested in connecting about a project.',
            'Thanks for reaching out. When works for you?',
            'I can do a call this week. How about Thursday?',
            'Sounds good. I\'ll send the details over.',
            'Let me know if you have questions.',
            'The scope looks clear. Happy to discuss.',
            'We\'re targeting a March start. Does that work?',
            'I\'ve shared the repo. Take a look when you can.',
            'Any availability next week for a quick sync?',
            'Perfect, looking forward to it!',
        ];

        foreach ($otherUsers as $i => $other) {
            [$conv] = Conversation::findOrCreateBetween($user->id, $other->id);

            if ($conv->messages()->exists()) {
                continue;
            }

            $count = rand(1, 3);
            $last = null;
            for ($m = 0; $m < $count; $m++) {
                $sender = $m % 2 === 0 ? $user : $other;
                $last = Message::factory()->create([
                    'conversation_id' => $conv->id,
                    'user_id' => $sender->id,
                    'body' => $bodies[($i + $m) % count($bodies)],
                ]);
            }
            $conv->update(['last_message_id' => $last->id]);
        }
    }
}
