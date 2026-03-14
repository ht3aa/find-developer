<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageAttachment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $searchTerm = is_string($search) ? trim($search) : '';

        $query = Message::query()
            ->with(['user:id,name,email', 'conversation.participants:id,name'])
            ->withCount('attachments')
            ->orderByDesc('created_at');

        if ($searchTerm !== '') {
            $term = '%'.addcslashes($searchTerm, '%_\\').'%';
            $query->where(function ($q) use ($term) {
                $q->where('body', 'like', $term)
                    ->orWhereHas('user', fn ($sub) => $sub->where('name', 'like', $term)->orWhere('email', 'like', $term));
            });
        }

        $messages = $query->paginate(20)->withQueryString()->through(fn (Message $m) => [
            'id' => $m->id,
            'conversation_id' => $m->conversation_id,
            'user' => $m->user ? [
                'id' => $m->user->id,
                'name' => $m->user->name,
                'email' => $m->user->email,
            ] : null,
            'body' => $m->body,
            'body_preview' => $m->body ? \Illuminate\Support\Str::limit(strip_tags($m->body), 100) : null,
            'attachments_count' => $m->attachments_count,
            'created_at' => $m->created_at->toIso8601String(),
        ]);

        return Inertia::render('Messages/DashboardIndex', [
            'messages' => $messages,
            'filters' => ['search' => $searchTerm],
        ]);
    }

    public function show(Message $message): Response
    {
        $message->load(['user:id,name,email', 'conversation.participants:id,name,email', 'attachments']);

        return Inertia::render('Messages/DashboardShow', [
            'message' => [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'user' => $message->user ? [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'email' => $message->user->email,
                ] : null,
                'body' => $message->body,
                'conversation_participants' => $message->conversation?->participants->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'email' => $p->email,
                ]) ?? [],
                'attachments' => $message->attachments->map(fn (MessageAttachment $a) => [
                    'id' => $a->id,
                    'file_name' => $a->file_name,
                    'file_path' => $a->file_path,
                    'file_type' => $a->file_type,
                    'file_size' => $a->file_size,
                    'file_url' => $a->file_url,
                    'created_at' => $a->created_at->toIso8601String(),
                ]),
                'created_at' => $message->created_at->toIso8601String(),
            ],
        ]);
    }
}
