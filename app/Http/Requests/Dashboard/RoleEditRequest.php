<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Role;

class RoleEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        $role = $this->route('role');

        if (! $role instanceof Role) {
            return false;
        }

        return $this->user()->can('update', $role);
    }

    public function rules(): array
    {
        return [];
    }
}
