<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Enums\UserType;
use App\Models\User;
use App\Models\UserService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserService>
 */
class UserServiceFactory extends Factory
{
    protected $model = UserService::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $serviceNames = [
            'Technical Consultation',
            'Code Review Service',
            'Project Planning',
            'Architecture Design',
            'Performance Optimization',
            'Security Audit',
            'Database Design',
            'API Development',
            'Frontend Development',
            'Backend Development',
            'Full Stack Development',
            'DevOps Setup',
            'CI/CD Pipeline',
            'Cloud Migration',
            'Technical Support',
        ];

        $name = fake()->randomElement($serviceNames);
        $price = fake()->boolean(70) ? fake()->numberBetween(50000, 500000) : null;
        $timeMinutes = fake()->boolean(60) ? fake()->randomElement([30, 60, 90, 120, 180]) : null;

        return [
            'user_id' => User::factory()->state([
                'user_type' => UserType::DEVELOPER,
            ]),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 99999),
            'description' => fake()->boolean(80) ? fake()->paragraph(3) : null,
            'price' => $price,
            'price_currency' => fake()->randomElement(Currency::cases()),
            'is_active' => fake()->boolean(85),
            'time_minutes' => $timeMinutes,
        ];
    }

    /**
     * Indicate that the service is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the service is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the service is free.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => null,
        ]);
    }

    /**
     * Indicate that the service has a specific price.
     */
    public function withPrice(int $price, Currency $currency = Currency::IQD): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $price,
            'price_currency' => $currency,
        ]);
    }
}
