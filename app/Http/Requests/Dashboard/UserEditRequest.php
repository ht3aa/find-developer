<?php

namespace App\Http\Requests\Dashboard;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        if (! $user instanceof User) {
            return false;
        }

        return $this->user()->can('update', $user);
    }

    public function rules(): array
    {
        return [];
    }
}
