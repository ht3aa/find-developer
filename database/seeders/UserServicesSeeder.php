<?php

namespace Database\Seeders;

use App\Enums\Currency;
use App\Enums\UserType;
use App\Models\User;
use App\Models\UserService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing services
        UserService::withoutGlobalScopes()->get()->each(fn ($s) => $s->delete());

        // Services page filters by HR users, so create HR users
        $hrUsers = User::where('user_type', UserType::HR)->get();

        if ($hrUsers->isEmpty()) {
            $hrUsers = collect();
            $hrNames = [
                ['name' => 'TechHire Solutions', 'email' => 'contact@techhire.dev'],
                ['name' => 'DevRecruit Agency', 'email' => 'hire@devrecruit.dev'],
                ['name' => 'CodeTalent HR', 'email' => 'services@codetalent.dev'],
            ];

            foreach ($hrNames as $hrData) {
                $hrUsers->push(User::factory()->create([
                    'name' => $hrData['name'],
                    'email' => $hrData['email'],
                    'user_type' => UserType::HR,
                ]));
            }
        }

        // Define sample services
        $servicesData = [
            [
                'name' => 'Technical Consultation',
                'description' => '<p>Expert technical consultation for your development projects. Get personalized advice on architecture, best practices, and technology stack selection.</p><p>Our consultants have 10+ years of experience working with startups and enterprises. We cover topics including <strong>microservices design</strong>, cloud infrastructure planning, CI/CD pipeline setup, and performance bottleneck analysis. You will receive a detailed written report with actionable recommendations after each session.</p>',
                'price' => 150000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Code Review Service',
                'description' => '<p>Comprehensive code review to improve code quality, identify bugs, and suggest improvements.</p><p>Our senior developers will go through your codebase line by line, checking for <strong>security vulnerabilities</strong>, performance issues, code duplication, and adherence to best practices. We review architecture patterns, database queries, API design, and test coverage. You will receive a detailed PDF report with prioritized findings, code examples showing recommended fixes, and a follow-up 30-minute call to discuss the results.</p>',
                'price' => 200000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 90,
                'is_active' => true,
            ],
            [
                'name' => 'Project Planning',
                'description' => '<p>Strategic project planning and roadmap development. We help you define project scope, timeline, and resource allocation for maximum efficiency.</p><p>This service includes stakeholder interviews, requirements gathering, technical feasibility analysis, and the creation of a detailed project plan with milestones and deliverables. We use agile methodologies to ensure flexibility while maintaining clear goals and deadlines throughout the development lifecycle.</p>',
                'price' => 300000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Architecture Design',
                'description' => '<p>Design scalable and maintainable software architecture for your applications. Includes comprehensive documentation and system diagrams.</p><p>We analyze your business requirements and design a robust architecture that supports growth. This covers database schema design, API structure, caching strategies, message queuing, third-party integration patterns, and deployment architecture. Deliverables include <strong>C4 model diagrams</strong>, data flow diagrams, and a technical decision document explaining every architectural choice.</p>',
                'price' => 400000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 180,
                'is_active' => true,
            ],
            [
                'name' => 'Performance Optimization',
                'description' => '<p>Analyze and optimize your application performance. Improve speed, reduce resource usage, and enhance user experience across all platforms.</p><p>We use profiling tools to identify bottlenecks in your backend, frontend, and database layers. Our optimization process includes query optimization, caching implementation, lazy loading strategies, image and asset optimization, and server configuration tuning. Typical results show <strong>40-70% improvement</strong> in page load times and significant reduction in server costs.</p>',
                'price' => 250000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Security Audit',
                'description' => '<p>Comprehensive security audit of your application following OWASP Top 10 guidelines.</p><p>Our security experts will perform both automated scanning and manual penetration testing to identify vulnerabilities including SQL injection, XSS, CSRF, authentication flaws, and authorization bypasses. We test API endpoints, file upload mechanisms, session management, and data encryption. The final report includes a risk assessment matrix, step-by-step remediation guides, and a re-testing session after fixes are applied.</p>',
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
            [
                'name' => 'Developer Recruitment',
                'description' => '<p>End-to-end developer recruitment service. We find, screen, and present the best candidates for your team.</p><p>Our process includes writing job descriptions, sourcing candidates from our network of 500+ developers, conducting technical screening interviews, skills assessments, and cultural fit evaluations. We handle all communication with candidates and present you with a shortlist of <strong>3-5 top matches</strong> along with detailed profiles, code samples, and our assessment notes. We offer a 30-day replacement guarantee if the hire does not work out.</p>',
                'price' => 750000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 300,
                'is_active' => true,
            ],
            [
                'name' => 'Team Building Workshop',
                'description' => '<p>Interactive workshop designed to build stronger, more collaborative development teams.</p><p>This full-day workshop covers effective communication in remote and hybrid teams, agile methodology training (Scrum and Kanban), pair programming exercises, code review best practices, and conflict resolution strategies. Each participant receives a workbook and access to follow-up resources. We customize the workshop content based on your team size, experience level, and specific challenges.</p>',
                'price' => 500000,
                'price_currency' => Currency::IQD,
                'time_minutes' => 180,
                'is_active' => true,
            ],
        ];

        // Assign services to HR users
        foreach ($hrUsers as $hrUser) {
            // Each HR user gets 3-5 services
            $servicesCount = rand(3, 5);
            $selectedServices = fake()->randomElements($servicesData, $servicesCount);

            foreach ($selectedServices as $serviceData) {
                $slug = Str::slug($serviceData['name']) . '-' . $hrUser->id;

                UserService::create([
                    'user_id' => $hrUser->id,
                    'name' => $serviceData['name'],
                    'slug' => $slug,
                    'description' => $serviceData['description'],
                    'price' => $serviceData['price'],
                    'price_currency' => $serviceData['price_currency'],
                    'time_minutes' => $serviceData['time_minutes'],
                    'is_active' => $serviceData['is_active'],
                ]);
            }
        }
    }
}
