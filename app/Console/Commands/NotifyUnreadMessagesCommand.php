<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Models\User;
use App\Notifications\UnreadMessagesNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotifyUnreadMessagesCommand extends Command
{
    protected $signature = 'messages:notify-unread';

    protected $description = 'Send email to developers with unread messages that are older than 2 days';

    public function handle(): int
    {
        $threshold = now()->subDays(2);

        $participants = DB::table('conversation_participants')->get();

        $usersToNotify = [];

        foreach ($participants as $participant) {
            $conversation = Conversation::find($participant->conversation_id);
            if (! $conversation) {
                continue;
            }

            $unreadCount = $conversation->unreadCountFor($participant->user_id);
            if ($unreadCount === 0) {
                continue;
            }

            $newestUnreadAt = $conversation->newestUnreadMessageCreatedAtFor($participant->user_id);
            if (! $newestUnreadAt || $newestUnreadAt->greaterThan($threshold)) {
                continue;
            }

            $usersToNotify[$participant->user_id] = ($usersToNotify[$participant->user_id] ?? 0) + $unreadCount;
        }

        $sent = 0;
        foreach ($usersToNotify as $userId => $totalUnread) {
            $user = User::find($userId);
            if ($user) {
                $user->notify(new UnreadMessagesNotification($totalUnread));
                $sent++;
            }
        }

        $this->info("Sent {$sent} unread message notification(s).");

        return self::SUCCESS;
    }
}
