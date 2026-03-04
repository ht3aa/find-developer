<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Developer;
use App\Models\Scopes\ApprovedScope;
use Illuminate\Foundation\Http\FormRequest;

class DeveloperEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        $developer = Developer::withoutGlobalScope(ApprovedScope::class)
            ->find($this->route('developer'));

        if (! $developer instanceof Developer) {
            return false;
        }

        return $this->user()->can('update', $developer);
    }

    public function rules(): array
    {
        return [];
    }
}
