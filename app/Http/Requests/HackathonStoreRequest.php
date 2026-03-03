<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HackathonStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Hackathon::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('reward_badge_id') && $this->reward_badge_id === '') {
            $this->merge(['reward_badge_id' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:500'],
            'youtube_url' => ['nullable', 'string', 'url', 'max:500'],
            'reward_badge_id' => ['nullable', 'integer', 'exists:badges,id'],
            'reward_description' => ['nullable', 'string', 'max:1000'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
