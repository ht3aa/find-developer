<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'ht3aa2001@gmail.com',
            'password' => Hash::make('1'),
        ]);

        // Seed job titles and skills first, then developers, then developer projects, then services and appointments
        $this->call([
            JobTitlesSeeder::class,
            SkillsSeeder::class,
            DevelopersSeeder::class,
            MoreDevelopersSeeder::class,
            DeveloperProjectsSeeder::class,
            UserServicesSeeder::class,
            UserAppointmentsSeeder::class,
            ConversationSeeder::class,
        ]);
    }
}
