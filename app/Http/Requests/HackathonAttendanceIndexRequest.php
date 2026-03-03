<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HackathonAttendanceIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
