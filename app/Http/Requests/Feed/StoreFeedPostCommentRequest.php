<?php

namespace App\Http\Requests\Feed;

use App\Models\FeedPostComment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFeedPostCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', FeedPostComment::class);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:feed_post_comments,id'],
        ];
    }
}
