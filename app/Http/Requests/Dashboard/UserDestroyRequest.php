<?php

namespace App\Http\Requests\Dashboard;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        if (! $user instanceof User) {
            return false;
        }

        return $this->user()->can('delete', $user);
    }

    public function rules(): array
    {
        return [];
    }
}
