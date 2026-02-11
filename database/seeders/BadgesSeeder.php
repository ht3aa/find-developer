<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Developer;
use App\Models\UserService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BadgesSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing badge data
        DB::table('developer_badge')->delete();
        DB::table('badge_user_service')->delete();
        Badge::query()->delete();

        $badges = [
            [
                'name' => 'Experience Validated',
                'description' => 'A developer with this badge has passed a Experience verification assessment. The assessment is conducted through an individual interview, where the developer demonstrates their skills and knowledge.',
                'icon' => 'badge-check',
                'color' => '#22c55e',
            ],
            [
                'name' => 'Found Security Issue',
                'description' => 'The developer that have this badge means he/she found a security issue and report it to the admins.',
                'icon' => 'shield-alert',
                'color' => '#a855f7',
            ],
            [
                'name' => 'Many Contact',
                'description' => 'The developer that have this badge mean he/she have great soft skills at work and outside work. He/she have many contacts in tech world.',
                'icon' => 'users',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Passion Developer',
                'description' => 'This badge indicates that the developer brings positive energy, shows strong readiness to take on tasks, adapts well to company environments, and reliably delivers results.',
                'icon' => 'flame',
                'color' => '#f97316',
            ],
            [
                'name' => 'Platform Contributor',
                'description' => 'This Badge state that the developer have 1 or more contribution on the find developer platform.',
                'icon' => 'code-xml',
                'color' => '#14b8a6',
            ],
            [
                'name' => 'Soft Skills',
                'description' => 'The developer that have this badge mean he/she have great soft skills at work.',
                'icon' => 'star',
                'color' => '#f59e0b',
            ],
            [
                'name' => 'The Founder',
                'description' => 'This badge is dedicated to the founder of the find developer platform.',
                'icon' => 'rocket',
                'color' => '#ef4444',
            ],
        ];

        foreach ($badges as $badgeData) {
            $badgeData['slug'] = Str::slug($badgeData['name']);
            Badge::create($badgeData);
        }

        // Assign random badges to developers
        $allBadges = Badge::all();
        $developers = Developer::all();

        foreach ($developers as $developer) {
            // Each developer gets 1-4 random badges
            $randomBadges = $allBadges->random(rand(1, min(4, $allBadges->count())));
            $developer->badges()->attach($randomBadges->pluck('id'));
        }

        // Assign random badges to user services
        $services = UserService::withoutGlobalScopes()->get();

        foreach ($services as $service) {
            // Each service gets 1-3 random badges
            $randomBadges = $allBadges->random(rand(1, min(3, $allBadges->count())));
            $service->badges()->attach($randomBadges->pluck('id'));
        }
    }
}
