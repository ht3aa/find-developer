<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Badge;
use Illuminate\Foundation\Http\FormRequest;

class BadgeEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        $badge = $this->route('badge');

        if (! $badge instanceof Badge) {
            return false;
        }

        return $this->user()->can('update', $badge);
    }

    public function rules(): array
    {
        return [];
    }
}
