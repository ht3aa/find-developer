<?php

namespace Database\Seeders;

use App\Models\Developer;
use App\Models\DeveloperProject;
use Illuminate\Database\Seeder;

class DeveloperProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $developers = Developer::all();

        if ($developers->isEmpty()) {
            $this->command->warn('No developers found. Please run DevelopersSeeder first.');

            return;
        }

        // Sample projects for each developer
        $projectTemplates = [
            [
                'title' => 'E-Commerce Platform',
                'description' => 'A full-featured e-commerce platform with shopping cart, payment integration, and admin dashboard.',
                'link' => 'https://example.com/ecommerce',
            ],
            [
                'title' => 'Task Management App',
                'description' => 'A collaborative task management application with real-time updates and team collaboration features.',
                'link' => 'https://example.com/taskmanager',
            ],
            [
                'title' => 'Social Media Dashboard',
                'description' => 'Analytics dashboard for social media management with real-time metrics and reporting.',
                'link' => 'https://example.com/social-dashboard',
            ],
            [
                'title' => 'Mobile Banking App',
                'description' => 'Secure mobile banking application with biometric authentication and transaction history.',
                'link' => 'https://example.com/banking-app',
            ],
            [
                'title' => 'Learning Management System',
                'description' => 'Comprehensive LMS with course creation, student enrollment, and progress tracking.',
                'link' => 'https://example.com/lms',
            ],
            [
                'title' => 'Restaurant Ordering System',
                'description' => 'Online ordering system for restaurants with menu management and order tracking.',
                'link' => 'https://example.com/restaurant-ordering',
            ],
        ];

        foreach ($developers as $developer) {
            // Each developer gets 2-4 random projects
            $numberOfProjects = rand(2, 4);
            $selectedProjects = array_rand($projectTemplates, $numberOfProjects);

            // Ensure we have an array even if only one project
            if (! is_array($selectedProjects)) {
                $selectedProjects = [$selectedProjects];
            }

            foreach ($selectedProjects as $index) {
                DeveloperProject::create([
                    'developer_id' => $developer->id,
                    'title' => $projectTemplates[$index]['title'],
                    'description' => $projectTemplates[$index]['description'],
                    'link' => $projectTemplates[$index]['link'],
                ]);
            }
        }

        $this->command->info('Created developer projects for '.$developers->count().' developers.');
    }
}
