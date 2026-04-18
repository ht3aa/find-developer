<?php

namespace App\Http\Requests\Feed;

use App\Models\FeedPost;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFeedPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', FeedPost::class);
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
        ];
    }
}
