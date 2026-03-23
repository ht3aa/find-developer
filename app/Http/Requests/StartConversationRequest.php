<?php

namespace App\Http\Requests;

use App\Models\Message;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StartConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'recipient_id' => ['required', 'integer', 'exists:users,id', function (string $attribute, mixed $value, \Closure $fail) {
                if ((int) $value === $this->user()->id) {
                    $fail('You cannot start a conversation with yourself.');
                }
            }],
            'body' => ['nullable', 'string', 'max:10000'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,txt,zip'],
            'reply_to_id' => [
                'nullable',
                'integer',
                'exists:messages,id',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! $value) {
                        return;
                    }
                    $userId = $this->user()->id;
                    $recipientId = (int) $this->input('recipient_id');
                    $message = Message::with('conversation.participants')->find($value);
                    if (! $message) {
                        return;
                    }
                    $participantIds = $message->conversation->participants->pluck('id')->all();
                    if (! in_array($userId, $participantIds) || ! in_array($recipientId, $participantIds)) {
                        $fail('The reply must be to a message in a conversation with this user.');
                    }
                },
            ],
        ];
    }

    public function hasContent(): bool
    {
        return ! empty($this->body) || ! empty($this->file('attachments'));
    }
}
