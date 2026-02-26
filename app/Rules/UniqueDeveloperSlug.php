<?php

namespace App\Rules;

use App\Models\Developer;
use App\Models\Scopes\ApprovedScope;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class UniqueDeveloperSlug implements ValidationRule
{
    public function __construct(
        public ?int $ignoreId = null
    ) {}

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $slug = Str::slug($value);

        if (Developer::withoutGlobalScope(ApprovedScope::class)
            ->where('slug', $slug)
            ->when($this->ignoreId, function ($query) {
                $query->where('id', '!=', $this->ignoreId);
            })
            ->exists()
        ) {
            $fail('A developer with this name already exists. Please add more details to your name to make it unique.');
        }
    }
}
