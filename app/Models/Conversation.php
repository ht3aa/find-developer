<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_message_id',
    ];

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Find an existing 1-to-1 conversation between two users, or create a new one.
     *
     * @return array{0: self, 1: bool} The conversation and whether it was newly created.
     */
    public static function findOrCreateBetween(int $userIdA, int $userIdB): array
    {
        $conversation = self::whereHas('participants', fn ($q) => $q->where('user_id', $userIdA))
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userIdB))
            ->withCount('participants')
            ->get()
            ->firstWhere('participants_count', 2);

        if ($conversation) {
            return [$conversation, false];
        }

        $conversation = self::create();
        $conversation->participants()->attach([$userIdA, $userIdB]);

        return [$conversation, true];
    }

    public function unreadCountFor(int $userId): int
    {
        $participant = $this->participants()->where('user_id', $userId)->first();

        if (! $participant) {
            return 0;
        }

        $lastReadAt = $participant->pivot->last_read_at;

        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->when($lastReadAt, fn ($q) => $q->where('created_at', '>', $lastReadAt))
            ->count();
    }

    /**
     * Get the created_at of the newest unread message for a user, or null if none.
     */
    public function newestUnreadMessageCreatedAtFor(int $userId): ?\Carbon\Carbon
    {
        $participant = $this->participants()->where('user_id', $userId)->first();

        if (! $participant) {
            return null;
        }

        $lastReadAt = $participant->pivot->last_read_at;

        $message = $this->messages()
            ->where('user_id', '!=', $userId)
            ->when($lastReadAt, fn ($q) => $q->where('created_at', '>', $lastReadAt))
            ->orderByDesc('created_at')
            ->first();

        return $message?->created_at;
    }
}
