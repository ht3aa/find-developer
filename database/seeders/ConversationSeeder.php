<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Developer;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testUser = User::where('email', 'ht3aa2001@gmail.com')->first();
        if (! $testUser) {
            $this->command->warn('User (ht3aa2001@gmail.com) not found. Skipping conversation seed.');

            return;
        }

        $developerUsers = Developer::whereNotNull('user_id')
            ->where('user_id', '!=', $testUser->id)
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter()
            ->values();

        $otherUsers = $developerUsers->isNotEmpty()
            ? $developerUsers
            : collect(User::factory(30)->create());
        $otherUsers = $otherUsers->filter(fn ($u) => $u->id !== $testUser->id)->values();

        $messageBodies = [
            'Hi! I saw your profile and would like to connect.',
            'Thanks for your interest. When would you be available for a chat?',
            'I have a project that might be a good fit. Can we schedule a call?',
            'Sure, I can do Thursday afternoon. Does that work for you?',
            'Great, looking forward to it!',
            'Let me know if you have any questions about the role.',
            'I\'ve shared the project details. Take a look when you can.',
            'Thanks for the quick response!',
            'Would you be interested in a quick intro call this week?',
            'I\'ll send over the brief. Let me know your thoughts.',
            'Perfect, I\'ve added you to the project channel.',
            'The deadline is flexible. No rush on your side.',
            'Happy to answer any technical questions.',
            'We can discuss compensation in the next call.',
            'Looking forward to working together!',
            'I\'ve updated the spec based on your feedback.',
            'Can we reschedule to next Tuesday?',
            'Sounds good. I\'ll follow up then.',
            'Thanks for the detailed response.',
            'Let me know when you\'re free for a sync.',
            'The team loved your portfolio. Really impressive work.',
            'I\'ll loop in the hiring manager for the next round.',
            'What\'s your rate for a 3-month contract?',
            'We\'re targeting a Q2 start. Does that align?',
            'Sent you a calendar invite for Friday 2pm.',
            'No worries, we can do async if that\'s easier.',
            'The codebase is on GitHub. I\'ll add you to the repo.',
            'Any blockers on your side we should know about?',
            'We\'ll send the offer by end of week.',
            'Excited to have you join the team!',
        ];

        foreach ($otherUsers as $index => $otherUser) {
            [$conversation] = Conversation::findOrCreateBetween($testUser->id, $otherUser->id);

            $messageCount = $conversation->messages()->count();
            if ($messageCount > 0) {
                continue;
            }

            $messagesToAdd = rand(2, 6);
            $lastMessage = null;
            for ($m = 0; $m < $messagesToAdd; $m++) {
                $sender = $m % 2 === 0 ? $testUser : $otherUser;
                $lastMessage = Message::factory()->create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $sender->id,
                    'body' => $messageBodies[($index + $m) % count($messageBodies)],
                ]);
            }

            $conversation->update(['last_message_id' => $lastMessage->id]);
        }

        if ($developerUsers->count() >= 2) {
            $this->seedDeveloperConversations($developerUsers, $messageBodies);
        }
    }

    private function seedDeveloperConversations($developerUsers, array $messageBodies): void
    {
        $users = $developerUsers->shuffle()->values();
        $count = min(25, (int) floor($users->count() / 2));

        for ($i = 0; $i < $count; $i++) {
            $userA = $users->get($i * 2);
            $userB = $users->get($i * 2 + 1);
            if (! $userA || ! $userB || $userA->id === $userB->id) {
                continue;
            }

            [$conversation] = Conversation::findOrCreateBetween($userA->id, $userB->id);

            if ($conversation->messages()->count() > 0) {
                continue;
            }

            $messagesToAdd = rand(1, 4);
            $lastMessage = null;
            for ($m = 0; $m < $messagesToAdd; $m++) {
                $sender = $m % 2 === 0 ? $userA : $userB;
                $lastMessage = Message::factory()->create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $sender->id,
                    'body' => $messageBodies[($i + $m) % count($messageBodies)],
                ]);
            }

            $conversation->update(['last_message_id' => $lastMessage->id]);
        }
    }
}
