<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\BlogStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeveloperBlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->developer !== null
            || $this->user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string', 'max:100000'],
            'featured_image' => ['nullable', 'string', 'max:500'],
        ];

        if ($this->user()->isSuperAdmin()) {
            $rules['status'] = ['required', Rule::enum(BlogStatus::class)];
            $rules['published_at'] = ['nullable', 'date'];
        }

        return $rules;
    }
}
