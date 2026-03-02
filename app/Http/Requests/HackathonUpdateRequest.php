<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HackathonUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
        if ($this->has('current_team_id_to_vote') && $this->current_team_id_to_vote === '') {
            $this->merge(['current_team_id_to_vote' => null]);
        }
        if (! $this->has('enable_voting')) {
            $this->merge(['enable_voting' => false]);
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
            'current_team_id_to_vote' => ['nullable', 'integer', 'exists:hackathon_teams,id'],
            'enable_voting' => ['boolean'],
        ];
    }
}
