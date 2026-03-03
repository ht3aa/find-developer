<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Developer;
use Illuminate\Foundation\Http\FormRequest;

class DeveloperIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Developer::class);
    }

    public function rules(): array
    {
        return [];
    }
}
