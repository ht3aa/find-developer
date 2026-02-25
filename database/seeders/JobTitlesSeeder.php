<?php

namespace Database\Seeders;

use App\Models\JobTitle;
use Illuminate\Database\Seeder;

class JobTitlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobTitles = [
            [
                'name' => 'Full Stack Developer',
                'slug' => 'full-stack-developer',
                'description' => 'Proficient in both front-end and back-end development',
            ],
            [
                'name' => 'Frontend Developer',
                'slug' => 'frontend-developer',
                'description' => 'Specializes in user interface and client-side development',
            ],
            [
                'name' => 'Backend Developer',
                'slug' => 'backend-developer',
                'description' => 'Focuses on server-side logic and database management',
            ],
            [
                'name' => 'Mobile Developer',
                'slug' => 'mobile-developer',
                'description' => 'Develops applications for mobile platforms',
            ],
            [
                'name' => 'DevOps Engineer',
                'slug' => 'devops-engineer',
                'description' => 'Manages deployment, automation, and infrastructure',
            ],
            [
                'name' => 'UI/UX Designer',
                'slug' => 'ui-ux-designer',
                'description' => 'Designs user interfaces and user experiences',
            ],
        ];

        foreach ($jobTitles as $jobTitleData) {
            JobTitle::create($jobTitleData);
        }
    }
}
