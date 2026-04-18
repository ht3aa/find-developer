<?php

namespace Database\Factories;

use App\Models\FeedPost;
use App\Models\FeedPostComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeedPostComment>
 */
class FeedPostCommentFactory extends Factory
{
    protected $model = FeedPostComment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'feed_post_id' => FeedPost::factory(),
            'parent_id' => null,
            'user_id' => User::factory(),
            'body' => fake()->sentence(),
        ];
    }
}
