<?php

namespace Database\Seeders;

use App\Enums\Currency;
use App\Enums\UserType;
use App\Models\User;
use App\Models\UserService;
use Illuminate\Database\Seeder;

class UserServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some CLIENT users first if they don't exist
        $clients = User::where('user_type', UserType::CLIENT)->get();

        if ($clients->isEmpty()) {
            // Create 5 client users
            $clients = User::factory(5)->create([
                'user_type' => UserType::CLIENT,
            ]);
        }

        // Define sample services for each client
        $servicesData = [
            [
                'name' => 'Technical Consultation',
                'description' => 'Expert technical consultation for your development projects. Get advice on architecture, best practices, and technology stack selection.',
                'price' => 150000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Code Review Service',
                'description' => 'Comprehensive code review to improve code quality, identify bugs, and suggest improvements. Includes detailed feedback report.',
                'price' => 200000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 90,
                'is_active' => true,
            ],
            [
                'name' => 'Project Planning',
                'description' => 'Strategic project planning and roadmap development. We help you define project scope, timeline, and resource allocation.',
                'price' => 300000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Architecture Design',
                'description' => 'Design scalable and maintainable software architecture for your applications. Includes documentation and diagrams.',
                'price' => 400000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 180,
                'is_active' => true,
            ],
            [
                'name' => 'Performance Optimization',
                'description' => 'Analyze and optimize your application performance. Improve speed, reduce resource usage, and enhance user experience.',
                'price' => 250000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Security Audit',
                'description' => 'Comprehensive security audit of your application. Identify vulnerabilities and receive recommendations for improvements.',
                'price' => 350000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 150,
                'is_active' => true,
            ],
            [
                'name' => 'Database Design',
                'description' => 'Professional database schema design and optimization. Ensure data integrity and optimal performance.',
                'price' => 200000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 90,
                'is_active' => true,
            ],
            [
                'name' => 'API Development',
                'description' => 'RESTful API development with documentation. Build robust and scalable APIs for your applications.',
                'price' => 500000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 240,
                'is_active' => true,
            ],
            [
                'name' => 'Free Consultation',
                'description' => 'Initial free consultation to discuss your project needs and requirements. No obligation.',
                'price' => null,
                'price_currency' => Currency::IQD,
                'time_minutes' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Technical Support',
                'description' => 'Ongoing technical support for your development projects. Get help when you need it.',
                'price' => 100000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 60,
                'is_active' => true,
            ],
        ];

        // Assign services to clients
        foreach ($clients as $index => $client) {
            // Each client gets 2-4 services
            $servicesCount = rand(2, 4);
            $selectedServices = fake()->randomElements($servicesData, $servicesCount);

            foreach ($selectedServices as $serviceData) {
                UserService::create([
                    'user_id' => $client->id,
                    'name' => $serviceData['name'],
                    'slug' => \Illuminate\Support\Str::slug($serviceData['name']),
                    'description' => $serviceData['description'],
                    'price' => $serviceData['price'],
                    'price_currency' => $serviceData['price_currency'],
                    'time_minutes' => $serviceData['time_minutes'],
                    'is_active' => $serviceData['is_active'],
                ]);
            }
        }

        // Create some additional random services using factory
        UserService::factory(10)->create();
    }
}
