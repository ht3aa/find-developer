<?php

namespace App\Http\Requests\Dashboard;

use App\Models\DeveloperBlog;
use Illuminate\Foundation\Http\FormRequest;

class DeveloperBlogIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', DeveloperBlog::class);
    }

    public function rules(): array
    {
        return [];
    }
}
