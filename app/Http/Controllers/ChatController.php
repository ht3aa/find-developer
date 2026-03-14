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

        return Inertia::render('Messages/Index', [
            'selectedConversationId' => null,
            'messages' => ['data' => [], 'has_more' => false],
            'selectedParticipant' => null,
            'sharingLinks' => $this->getSharingLinks($user),
        ]);
    }

    public function show(Request $request, Conversation $conversation): Response
    {
        $this->authorize('view', $conversation);

        $user = $request->user();

        $conversation->participants()
            ->updateExistingPivot($user->id, ['last_read_at' => now()]);

        $limit = 15;
        $messagesQuery = $conversation->messages()
            ->with(['user:id,name,email,user_type', 'attachments', 'parentMessage.user:id,name'])
            ->orderByDesc('created_at')
            ->limit($limit + 1);

        $messagesCollection = $messagesQuery->get();
        $hasMore = $messagesCollection->count() > $limit;
        $messagesCollection = $hasMore ? $messagesCollection->take($limit) : $messagesCollection;

        $messages = [
            'data' => $messagesCollection->map(fn (Message $m) => $this->messageToArray($m, $user->id))->values()->all(),
            'has_more' => $hasMore,
        ];

        $participant = $conversation->participants()
            ->where('user_id', '!=', $user->id)
            ->select('users.id', 'users.name', 'users.email', 'users.user_type')
            ->first();

        return Inertia::render('Messages/Index', [
            'selectedConversationId' => $conversation->id,
            'messages' => $messages,
            'selectedParticipant' => $participant ? [
                'id' => $participant->id,
                'name' => $participant->name,
                'email' => $participant->email,
                'user_type_label' => $participant->user_type?->getLabel() ?? '—',
            ] : null,
            'sharingLinks' => $this->getSharingLinks($user),
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
                'parent_message_id' => $request->validated('reply_to_id'),
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
            'parent_message_id' => $request->validated('reply_to_id'),
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

    /**
     * @return array{profileUrl: string|null, cvUrl: string|null}
     */
    private function getSharingLinks(User $user): array
    {
        $developer = $user->developer;

        return [
            'profileUrl' => $developer?->slug ? route('developers.show', $developer->slug) : null,
            'cvUrl' => $developer?->cv_path_url,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function messageToArray(Message $m, int $currentUserId): array
    {
        $arr = [
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
            ])->values()->all(),
            'is_own' => $m->user_id === $currentUserId,
            'created_at' => $m->created_at->toISOString(),
        ];

        if ($m->relationLoaded('parentMessage') && $m->parentMessage) {
            $p = $m->parentMessage;
            $arr['reply_to'] = [
                'id' => $p->id,
                'body' => $p->body,
                'user' => ['name' => $p->user->name ?? '—'],
            ];
        }

        return $arr;
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
