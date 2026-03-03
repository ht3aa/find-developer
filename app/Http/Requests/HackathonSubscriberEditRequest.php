<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HackathonSubscriberEditRequest extends FormRequest
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
