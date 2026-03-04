<?php

namespace App\Http\Requests\Dashboard;

use App\Models\DeveloperBlog;
use Illuminate\Foundation\Http\FormRequest;

class DeveloperBlogCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', DeveloperBlog::class);
    }

    public function rules(): array
    {
        return [];
    }
}
