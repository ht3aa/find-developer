<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MessageAttachment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MessageAttachmentController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $searchTerm = is_string($search) ? trim($search) : '';

        $query = MessageAttachment::query()
            ->with(['message.user:id,name,email'])
            ->orderByDesc('created_at');

        if ($searchTerm !== '') {
            $term = '%'.addcslashes($searchTerm, '%_\\').'%';
            $query->where(function ($q) use ($term) {
                $q->where('file_name', 'like', $term)
                    ->orWhere('file_type', 'like', $term);
            });
        }

        $attachments = $query->paginate(20)->withQueryString()->through(fn (MessageAttachment $a) => [
            'id' => $a->id,
            'message_id' => $a->message_id,
            'file_name' => $a->file_name,
            'file_type' => $a->file_type,
            'file_size' => $a->file_size,
            'file_url' => $a->file_url,
            'sender' => $a->message?->user ? [
                'id' => $a->message->user->id,
                'name' => $a->message->user->name,
                'email' => $a->message->user->email,
            ] : null,
            'created_at' => $a->created_at->toIso8601String(),
        ]);

        return Inertia::render('MessageAttachments/Index', [
            'attachments' => $attachments,
            'filters' => ['search' => $searchTerm],
        ]);
    }

    public function show(MessageAttachment $messageAttachment): Response
    {
        $messageAttachment->load(['message.user:id,name,email', 'message.conversation.participants:id,name,email']);

        return Inertia::render('MessageAttachments/Show', [
            'attachment' => [
                'id' => $messageAttachment->id,
                'message_id' => $messageAttachment->message_id,
                'file_name' => $messageAttachment->file_name,
                'file_path' => $messageAttachment->file_path,
                'file_type' => $messageAttachment->file_type,
                'file_size' => $messageAttachment->file_size,
                'file_url' => $messageAttachment->file_url,
                'sender' => $messageAttachment->message?->user ? [
                    'id' => $messageAttachment->message->user->id,
                    'name' => $messageAttachment->message->user->name,
                    'email' => $messageAttachment->message->user->email,
                ] : null,
                'conversation_participants' => $messageAttachment->message?->conversation?->participants->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'email' => $p->email,
                ]) ?? [],
                'created_at' => $messageAttachment->created_at->toIso8601String(),
            ],
        ]);
    }
}
