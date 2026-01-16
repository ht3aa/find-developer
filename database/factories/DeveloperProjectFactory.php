<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeveloperProject>
 */
class DeveloperProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'developer_id' => \App\Models\Developer::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'link' => fake()->url(),
        ];
    }
}
