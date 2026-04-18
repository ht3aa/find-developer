<?php

namespace App\Http\Requests\Feed;

use App\Models\FeedPost;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFeedPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var FeedPost $post */
        $post = $this->route('feedPost');

        return $this->user()->can('update', $post);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['file', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096'],
            'clear_images' => ['sometimes', 'boolean'],
        ];
    }
}
