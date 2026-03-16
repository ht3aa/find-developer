<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Newsletter;
use Illuminate\Foundation\Http\FormRequest;

class NewsletterDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $newsletter = $this->route('newsletter');

        if (! $newsletter instanceof Newsletter) {
            return false;
        }

        return $this->user()->can('delete', $newsletter);
    }

    public function rules(): array
    {
        return [];
    }
}
