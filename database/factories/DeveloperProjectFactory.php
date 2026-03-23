<?php

namespace Database\Factories;

use App\Models\Developer;
use App\Models\DeveloperProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeveloperProject>
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
            'developer_id' => Developer::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'link' => fake()->url(),
        ];
    }
}
