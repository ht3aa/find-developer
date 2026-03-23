<?php

namespace App\Http\Requests;

use App\Models\Message;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
        $conversationId = $this->route('conversation')?->id;

        return [
            'body' => ['nullable', 'string', 'max:10000'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,txt,zip'],
            'reply_to_id' => [
                'nullable',
                'integer',
                'exists:messages,id',
                function (string $attribute, mixed $value, \Closure $fail) use ($conversationId) {
                    if ($value && $conversationId) {
                        $exists = Message::where('id', $value)
                            ->where('conversation_id', $conversationId)
                            ->exists();
                        if (! $exists) {
                            $fail('The reply must be to a message in this conversation.');
                        }
                    }
                },
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (empty($this->body) && empty($this->file('attachments'))) {
                $validator->errors()->add('body', 'A message or attachment is required.');
            }
        });
    }
}
