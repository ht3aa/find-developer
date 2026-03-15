<?php

namespace App\Http\Resources;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Conversation $conversation */
        $conversation = $this->resource;
        $user = $request->user();
        $participant = $conversation->participants->first();
        $lastMessage = $conversation->lastMessage;

        return [
            'id' => $conversation->id,
            'participant' => $participant ? [
                'id' => $participant->id,
                'name' => $participant->name,
                'email' => $participant->email,
                'user_type_label' => $participant->user_type?->getLabel() ?? '—',
                'developer_slug' => $participant->developer?->slug,
            ] : null,
            'last_message' => $lastMessage ? [
                'id' => $lastMessage->id,
                'body' => $lastMessage->body,
                'user' => [
                    'id' => $lastMessage->user->id,
                    'name' => $lastMessage->user->name,
                    'user_type_label' => $lastMessage->user->user_type?->getLabel() ?? '—',
                ],
                'is_own' => $lastMessage->user_id === $user?->id,
                'created_at' => $lastMessage->created_at->toISOString(),
            ] : null,
            'unread_count' => $user ? $conversation->unreadCountFor($user->id) : 0,
            'updated_at' => $conversation->updated_at->toISOString(),
        ];
    }
}
