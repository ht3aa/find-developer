<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Hackathon;
use Illuminate\Foundation\Http\FormRequest;

class HackathonDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $hackathon = $this->route('hackathon');

        if (! $hackathon instanceof Hackathon) {
            return false;
        }

        return $this->user()->can('delete', $hackathon);
    }

    public function rules(): array
    {
        return [];
    }
}
