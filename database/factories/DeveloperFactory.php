<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Enums\DeveloperStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\UserType;
use App\Enums\WorldGovernorate;
use App\Models\Developer;
use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Developer>
 */
class DeveloperFactory extends Factory
{
    protected $model = Developer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();
        $jobTitle = JobTitle::query()->inRandomOrder()->first()
            ?? JobTitle::create(['name' => 'Developer', 'slug' => 'developer', 'is_active' => true]);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(6),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->boolean(70) ? fake()->phoneNumber() : null,
            'user_id' => User::factory()->state(['user_type' => UserType::DEVELOPER]),
            'job_title_id' => $jobTitle->id,
            'years_of_experience' => fake()->numberBetween(0, 20),
            'bio' => fake()->boolean(80) ? fake()->paragraph(3) : null,
            'portfolio_url' => fake()->boolean(40) ? fake()->url() : null,
            'github_url' => fake()->boolean(50) ? 'https://github.com/'.fake()->userName() : null,
            'linkedin_url' => fake()->boolean(40) ? 'https://linkedin.com/in/'.fake()->userName() : null,
            'cv_path' => null,
            'location' => fake()->randomElement(WorldGovernorate::cases()),
            'expected_salary_from' => fake()->boolean(60) ? fake()->numberBetween(500000, 2000000) : null,
            'expected_salary_to' => fake()->boolean(40) ? fake()->numberBetween(2000000, 5000000) : null,
            'salary_currency' => Currency::IQD,
            'is_available' => fake()->boolean(70),
            'special_needs' => false,
            'availability_type' => null,
            'recommended_by_us' => false,
            'status' => DeveloperStatus::APPROVED,
            'subscription_plan' => fake()->randomElement(SubscriptionPlan::cases()),
        ];
    }
}
