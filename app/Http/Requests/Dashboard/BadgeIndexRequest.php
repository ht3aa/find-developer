<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Badge;
use Illuminate\Foundation\Http\FormRequest;

class BadgeIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Badge::class);
    }

    public function rules(): array
    {
        return [];
    }
}
