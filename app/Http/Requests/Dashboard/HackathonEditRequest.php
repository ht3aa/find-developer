<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Hackathon;
use Illuminate\Foundation\Http\FormRequest;

class HackathonEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        $hackathon = $this->route('hackathon');

        if (! $hackathon instanceof Hackathon) {
            return false;
        }

        return $this->user()->can('update', $hackathon);
    }

    public function rules(): array
    {
        return [];
    }
}
