<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * List the authenticated user's conversations.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $conversations = $user->conversations()
            ->with([
                'lastMessage.user:id,name,user_type',
                'participants' => fn ($q) => $q->where('user_id', '!=', $user->id)
                    ->select('users.id', 'users.name', 'users.email', 'users.user_type')
                    ->with('developer:id,user_id,slug'),
            ])
            ->whereNotNull('last_message_id')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (Conversation $c) => [
                'id' => $c->id,
                'participant' => $c->participants->first() ? [
                    'id' => $c->participants->first()->id,
                    'name' => $c->participants->first()->name,
                    'email' => $c->participants->first()->email,
                    'user_type_label' => $c->participants->first()->user_type?->getLabel() ?? '—',
                    'developer_slug' => $c->participants->first()->developer?->slug,
                ] : null,
                'last_message' => $c->lastMessage ? [
                    'id' => $c->lastMessage->id,
                    'body' => $c->lastMessage->body,
                    'user' => [
                        'id' => $c->lastMessage->user->id,
                        'name' => $c->lastMessage->user->name,
                        'user_type_label' => $c->lastMessage->user->user_type?->getLabel() ?? '—',
                    ],
                    'is_own' => $c->lastMessage->user_id === $user->id,
                    'created_at' => $c->lastMessage->created_at->toISOString(),
                ] : null,
                'unread_count' => $c->unreadCountFor($user->id),
                'updated_at' => $c->updated_at->toISOString(),
            ])
            ->values()
            ->all();

        return response()->json(['data' => $conversations]);
    }
}
