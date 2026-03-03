<?php

namespace App\Http\Requests;

use App\Models\Hackathon;
use Illuminate\Foundation\Http\FormRequest;

class HackathonIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Hackathon::class);
    }

    public function rules(): array
    {
        return [];
    }
}
