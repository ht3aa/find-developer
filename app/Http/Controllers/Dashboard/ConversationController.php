<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConversationController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $searchTerm = is_string($search) ? trim($search) : '';

        $query = Conversation::query()
            ->with(['participants:id,name,email', 'lastMessage.user:id,name'])
            ->withCount('messages')
            ->orderByDesc('updated_at');

        if ($searchTerm !== '') {
            $term = '%'.addcslashes($searchTerm, '%_\\').'%';
            $query->whereHas('participants', fn ($q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term));
        }

        $conversations = $query->paginate(20)->withQueryString()->through(fn (Conversation $c) => [
            'id' => $c->id,
            'participants' => $c->participants->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'email' => $p->email,
            ]),
            'messages_count' => $c->messages_count,
            'last_message' => $c->lastMessage ? [
                'body' => $c->lastMessage->body,
                'user_name' => $c->lastMessage->user?->name,
                'created_at' => $c->lastMessage->created_at->toIso8601String(),
            ] : null,
            'created_at' => $c->created_at->toIso8601String(),
            'updated_at' => $c->updated_at->toIso8601String(),
        ]);

        return Inertia::render('Conversations/Index', [
            'conversations' => $conversations,
            'filters' => ['search' => $searchTerm],
        ]);
    }

    public function show(Conversation $conversation): Response
    {
        $conversation->load(['participants:id,name,email']);

        $messages = $conversation->messages()
            ->with(['user:id,name,email', 'attachments'])
            ->orderByDesc('created_at')
            ->paginate(50)
            ->withQueryString()
            ->through(fn (Message $m) => [
                'id' => $m->id,
                'user' => $m->user ? [
                    'id' => $m->user->id,
                    'name' => $m->user->name,
                    'email' => $m->user->email,
                ] : null,
                'body' => $m->body,
                'attachments_count' => $m->attachments->count(),
                'attachments' => $m->attachments->map(fn (MessageAttachment $a) => [
                    'id' => $a->id,
                    'file_name' => $a->file_name,
                    'file_type' => $a->file_type,
                    'file_size' => $a->file_size,
                ]),
                'created_at' => $m->created_at->toIso8601String(),
            ]);

        return Inertia::render('Conversations/Show', [
            'conversation' => [
                'id' => $conversation->id,
                'participants' => $conversation->participants->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'email' => $p->email,
                ]),
                'created_at' => $conversation->created_at->toIso8601String(),
                'updated_at' => $conversation->updated_at->toIso8601String(),
            ],
            'messages' => $messages,
        ]);
    }
}
