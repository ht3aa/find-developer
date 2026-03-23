<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\BlogStatus;
use App\Models\DeveloperBlog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeveloperBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        $developerBlog = $this->route('developer_blog');

        if (! $developerBlog instanceof DeveloperBlog) {
            return false;
        }

        return $this->user()->can('update', $developerBlog);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string', 'max:100000'],
            'featured_image' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
        ];

        if ($this->user()->isSuperAdmin()) {
            $rules['status'] = ['required', Rule::enum(BlogStatus::class)];
            $rules['published_at'] = ['nullable', 'date'];
        }

        return $rules;
    }
}
