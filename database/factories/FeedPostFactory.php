<?php

namespace Database\Factories;

use App\Models\FeedPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeedPost>
 */
class FeedPostFactory extends Factory
{
    protected $model = FeedPost::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'body' => fake()->paragraphs(2, true),
        ];
    }
}
