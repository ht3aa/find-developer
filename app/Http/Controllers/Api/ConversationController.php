<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * List the authenticated user's conversations (cursor-based, 15 per fetch).
     * Pass before_id (from last item) to load older conversations.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = 15;

        $query = $user->conversations()
            ->with([
                'lastMessage.user:id,name,user_type',
                'participants' => fn ($q) => $q->where('user_id', '!=', $user->id)
                    ->select('users.id', 'users.name', 'users.email', 'users.user_type')
                    ->with('developer:id,user_id,slug'),
            ])
            ->whereNotNull('last_message_id')
            ->orderByDesc('conversations.updated_at')
            ->orderByDesc('conversations.id');

        $beforeId = $request->integer('before_id');
        if ($beforeId > 0) {
            $query->where('conversations.id', '<', $beforeId);
        }

        $conversations = $query->limit($limit + 1)->get();
        $hasMore = $conversations->count() > $limit;
        if ($hasMore) {
            $conversations = $conversations->take($limit);
        }

        return response()->json([
            'data' => ConversationResource::collection($conversations),
            'meta' => [
                'has_more' => $hasMore,
            ],
        ]);
    }
}
