<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationMessageController extends Controller
{
    /**
     * Fetch older messages for a conversation (cursor-based, before_id).
     *
     * @return JsonResponse{data: array, has_more: bool}
     */
    public function index(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorize('view', $conversation);

        $beforeId = $request->integer('before_id');
        if ($beforeId <= 0) {
            return response()->json(['data' => [], 'has_more' => false]);
        }

        $limit = 15;
        $messages = $conversation->messages()
            ->with(['user:id,name,email,user_type', 'attachments'])
            ->where('id', '<', $beforeId)
            ->orderByDesc('created_at')
            ->limit($limit + 1)
            ->get();

        $hasMore = $messages->count() > $limit;
        if ($hasMore) {
            $messages = $messages->take($limit);
        }

        $user = $request->user();
        $data = $messages->map(fn (Message $m) => [
            'id' => $m->id,
            'conversation_id' => $m->conversation_id,
            'user' => [
                'id' => $m->user->id,
                'name' => $m->user->name,
                'email' => $m->user->email,
                'user_type_label' => $m->user->user_type?->getLabel() ?? '—',
            ],
            'body' => $m->body,
            'attachments' => $m->attachments->map(fn (MessageAttachment $a) => [
                'id' => $a->id,
                'file_name' => $a->file_name,
                'file_url' => $a->file_url,
                'file_type' => $a->file_type,
                'file_size' => $a->file_size,
            ]),
            'is_own' => $m->user_id === $user->id,
            'created_at' => $m->created_at->toISOString(),
        ])->values()->all();

        return response()->json([
            'data' => $data,
            'has_more' => $hasMore,
        ]);
    }
}
