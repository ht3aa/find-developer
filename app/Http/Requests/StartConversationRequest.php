<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
        ];
    }

    public function hasContent(): bool
    {
        return ! empty($this->body) || ! empty($this->file('attachments'));
    }
}
