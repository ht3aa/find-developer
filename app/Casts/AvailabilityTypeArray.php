<?php

namespace App\Casts;

use App\Enums\AvailabilityType;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AvailabilityTypeArray implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?array
    {
        if (is_null($value)) {
            return null;
        }

        $decoded = json_decode($value, true);

        if (! is_array($decoded)) {
            return null;
        }

        return array_map(
            fn ($item) => AvailabilityType::from($item),
            $decoded
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (is_null($value) || (is_array($value) && empty($value))) {
            return null;
        }

        if (! is_array($value)) {
            return null;
        }

        $values = array_map(
            fn ($item) => $item instanceof AvailabilityType ? $item->value : $item,
            array_filter($value) // Remove null/empty values
        );

        if (empty($values)) {
            return null;
        }

        return json_encode(array_values($values)); // Re-index array
    }
}
