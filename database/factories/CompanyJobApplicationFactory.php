<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\CompanyJob;
use App\Models\CompanyJobApplication;
use App\Models\Developer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyJobApplication>
 */
class CompanyJobApplicationFactory extends Factory
{
    protected $model = CompanyJobApplication::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_job_id' => CompanyJob::factory(),
            'developer_id' => Developer::factory(),
            'status' => ApplicationStatus::PENDING,
            'cover_message' => fake()->boolean(60) ? fake()->paragraph() : null,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ApplicationStatus::ACCEPTED,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ApplicationStatus::REJECTED,
        ]);
    }
}
