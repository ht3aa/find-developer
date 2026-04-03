<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Enums\JobStatus;
use App\Enums\WorldGovernorate;
use App\Models\CompanyJob;
use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CompanyJob>
 */
class CompanyJobFactory extends Factory
{
    protected $model = CompanyJob::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);
        $jobTitle = JobTitle::query()->inRandomOrder()->first()
            ?? JobTitle::create(['name' => 'Full Stack Developer', 'slug' => 'full-stack-developer', 'is_active' => true]);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(6),
            'description' => fake()->paragraphs(3, true),
            'company_name' => fake()->company(),
            'email' => fake()->companyEmail(),
            'contact_link' => fake()->boolean(50) ? fake()->url() : null,
            'location' => fake()->randomElement(WorldGovernorate::cases()),
            'job_title_id' => $jobTitle->id,
            'salary_from' => fake()->numberBetween(500_000, 2_000_000),
            'salary_to' => fake()->numberBetween(2_000_000, 5_000_000),
            'salary_currency' => Currency::IQD,
            'requirements' => fake()->boolean(70) ? fake()->paragraph() : null,
            'status' => JobStatus::PENDING,
            'first_payment_qi_confirmed' => false,
            'gitea_owner' => null,
            'gitea_repo_name' => null,
            'gitea_provisioned_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => JobStatus::APPROVED,
            'first_payment_qi_confirmed' => true,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => JobStatus::REJECTED,
        ]);
    }
}
