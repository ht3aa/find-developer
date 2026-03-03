<?php

namespace App\Http\Requests\Dashboard;

use App\Models\DeveloperBlog;
use Illuminate\Foundation\Http\FormRequest;

class DeveloperBlogDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $developerBlog = $this->route('developer_blog');

        if (! $developerBlog instanceof DeveloperBlog) {
            return false;
        }

        return $this->user()->can('delete', $developerBlog);
    }

    public function rules(): array
    {
        return [];
    }
}
