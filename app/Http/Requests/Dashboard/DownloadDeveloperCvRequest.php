<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class DownloadDeveloperCvRequest extends FormRequest
{
    public function authorize(): bool
    {
        $developer = $this->user()->developer;

        if (! $developer) {
            return true;
        }

        return $this->user()->can('updateDeveloperProfile', $developer);
    }

    public function rules(): array
    {
        return [];
    }
}
