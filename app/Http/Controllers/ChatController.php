<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartConversationRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use App\Notifications\NewConversationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $conversations = $user->conversations()
            ->with(['lastMessage.user:id,name', 'participants' => fn ($q) => $q->where('user_id', '!=', $user->id)->select('users.id', 'users.name', 'users.email')])
            ->whereNotNull('last_message_id')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (Conversation $c) => [
                'id' => $c->id,
                'participant' => $c->participants->first() ? [
                    'id' => $c->participants->first()->id,
                    'name' => $c->participants->first()->name,
                    'email' => $c->participants->first()->email,
                ] : null,
                'last_message' => $c->lastMessage ? [
                    'id' => $c->lastMessage->id,
                    'body' => $c->lastMessage->body,
                    'user' => [
                        'id' => $c->lastMessage->user->id,
                        'name' => $c->lastMessage->user->name,
                    ],
                    'is_own' => $c->lastMessage->user_id === $user->id,
                    'created_at' => $c->lastMessage->created_at->toISOString(),
                ] : null,
                'unread_count' => $c->unreadCountFor($user->id),
                'updated_at' => $c->updated_at->toISOString(),
            ]);

        return Inertia::render('Messages/Index', [
            'conversations' => $conversations,
            'selectedConversationId' => null,
            'messages' => [],
            'selectedParticipant' => null,
        ]);
    }

    public function show(Request $request, Conversation $conversation): Response
    {
        $this->authorize('view', $conversation);

        $user = $request->user();

        $conversation->participants()
            ->updateExistingPivot($user->id, ['last_read_at' => now()]);

        $messages = $conversation->messages()
            ->with(['user:id,name,email', 'attachments'])
            ->orderBy('created_at')
            ->get()
            ->map(fn (Message $m) => [
                'id' => $m->id,
                'conversation_id' => $m->conversation_id,
                'user' => [
                    'id' => $m->user->id,
                    'name' => $m->user->name,
                    'email' => $m->user->email,
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
            ]);

        $participant = $conversation->participants()
            ->where('user_id', '!=', $user->id)
            ->select('users.id', 'users.name', 'users.email')
            ->first();

        $conversations = $user->conversations()
            ->with(['lastMessage.user:id,name', 'participants' => fn ($q) => $q->where('user_id', '!=', $user->id)->select('users.id', 'users.name', 'users.email')])
            ->whereNotNull('last_message_id')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (Conversation $c) => [
                'id' => $c->id,
                'participant' => $c->participants->first() ? [
                    'id' => $c->participants->first()->id,
                    'name' => $c->participants->first()->name,
                    'email' => $c->participants->first()->email,
                ] : null,
                'last_message' => $c->lastMessage ? [
                    'id' => $c->lastMessage->id,
                    'body' => $c->lastMessage->body,
                    'user' => [
                        'id' => $c->lastMessage->user->id,
                        'name' => $c->lastMessage->user->name,
                    ],
                    'is_own' => $c->lastMessage->user_id === $user->id,
                    'created_at' => $c->lastMessage->created_at->toISOString(),
                ] : null,
                'unread_count' => $c->unreadCountFor($user->id),
                'updated_at' => $c->updated_at->toISOString(),
            ]);

        return Inertia::render('Messages/Index', [
            'conversations' => $conversations,
            'selectedConversationId' => $conversation->id,
            'messages' => $messages,
            'selectedParticipant' => $participant ? [
                'id' => $participant->id,
                'name' => $participant->name,
                'email' => $participant->email,
            ] : null,
        ]);
    }

    public function store(StartConversationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $recipientId = $request->validated('recipient_id');

        [$conversation, $isNew] = Conversation::findOrCreateBetween($user->id, $recipientId);

        if ($request->hasContent()) {
            $isFirstMessage = $conversation->messages()->doesntExist();

            $message = $conversation->messages()->create([
                'user_id' => $user->id,
                'body' => $request->validated('body'),
            ]);

            $this->storeAttachments($message, $request);

            $conversation->update(['last_message_id' => $message->id]);
            $conversation->touch();

            $conversation->participants()
                ->updateExistingPivot($user->id, ['last_read_at' => now()]);

            if ($isFirstMessage) {
                $recipient = User::find($recipientId);
                $recipient->notify(new NewConversationNotification($user));
            }

            return redirect()->route('messages.show', $conversation)
                ->with('success', 'Message sent.');
        }

        return redirect()->route('messages.show', $conversation);
    }

    public function sendMessage(StoreMessageRequest $request, Conversation $conversation): RedirectResponse
    {
        $this->authorize('sendMessage', $conversation);

        $user = $request->user();
        $isFirstMessage = $conversation->messages()->doesntExist();

        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'body' => $request->validated('body'),
        ]);

        $this->storeAttachments($message, $request);

        $conversation->update(['last_message_id' => $message->id]);
        $conversation->touch();

        $conversation->participants()
            ->updateExistingPivot($user->id, ['last_read_at' => now()]);

        if ($isFirstMessage) {
            $recipient = $conversation->participants()
                ->where('user_id', '!=', $user->id)
                ->first();

            $recipient?->notify(new NewConversationNotification($user));
        }

        return redirect()->route('messages.show', $conversation);
    }

    public function searchUsers(Request $request): \Illuminate\Http\JsonResponse
    {
        $search = $request->get('q', '');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $users = User::query()
            ->where('id', '!=', $request->user()->id)
            ->where(
                fn ($q) => $q
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
            )
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    private function storeAttachments(Message $message, Request $request): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            $path = $file->store('chat-attachments', ['disk' => 's3']);

            $message->attachments()->create([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }
}
