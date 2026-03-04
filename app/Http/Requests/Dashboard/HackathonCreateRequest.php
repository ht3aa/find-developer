<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Hackathon;
use Illuminate\Foundation\Http\FormRequest;

class HackathonCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Hackathon::class);
    }

    public function rules(): array
    {
        return [];
    }
}
