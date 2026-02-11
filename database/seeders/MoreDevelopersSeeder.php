<?php

namespace Database\Seeders;

use App\Enums\DeveloperStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\WorldGovernorate;
use App\Models\Developer;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MoreDevelopersSeeder extends Seeder
{
    /**
     * Number of additional developers to create.
     */
    protected int $count = 25;

    /**
     * Run the database seeds.
     * Adds additional developers with random Faker data. Run after DevelopersSeeder (and JobTitlesSeeder, SkillsSeeder).
     */
    public function run(): void
    {
        $faker = fake();
        $governorates = WorldGovernorate::cases();
        $plans = SubscriptionPlan::cases();
        $skillIds = Skill::pluck('id')->toArray();

        if (empty($skillIds)) {
            $this->command->warn('No skills found. Please run SkillsSeeder first.');

            return;
        }

        for ($i = 0; $i < $this->count; $i++) {
            $name = $faker->name();
            $slug = Str::slug($name);
            $years = $faker->numberBetween(1, 12);
            $salaryFrom = $faker->numberBetween(104_000_000, 260_000_000);
            $salaryTo = $salaryFrom + $faker->numberBetween(26_000_000, 65_000_000);

            $data = [
                'name' => $name,
                'slug' => $slug . '-' . ($i + 1),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'job_title_id' => $faker->numberBetween(1, 6),
                'years_of_experience' => $years,
                'bio' => $faker->paragraphs(2, true),
                'portfolio_url' => 'https://'.$slug.'.'.$faker->randomElement(['dev', 'com', 'io']),
                'github_url' => 'https://github.com/'.$slug,
                'linkedin_url' => 'https://linkedin.com/in/'.$slug,
                'location' => $faker->randomElement($governorates),
                'expected_salary_from' => $salaryFrom,
                'expected_salary_to' => $salaryTo,
                'is_available' => $faker->boolean(80),
                'status' => DeveloperStatus::APPROVED,
                'subscription_plan' => $faker->randomElement($plans),
            ];

            $developer = Developer::withoutGlobalScopes()
                ->firstOrCreate(
                    ['email' => $data['email']],
                    $data
                );

            $numSkills = $faker->numberBetween(3, min(8, count($skillIds)));
            $developerSkillIds = $faker->randomElements($skillIds, $numSkills);

            $developer->skills()->syncWithoutDetaching($developerSkillIds);
        }

        $faker->unique(true);
    }
}
