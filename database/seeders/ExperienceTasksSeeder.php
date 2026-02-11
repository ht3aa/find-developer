<?php

namespace Database\Seeders;

use App\Enums\Currency;
use App\Enums\ExperienceGain;
use App\Enums\ExperienceTaskStatus;
use App\Models\Developer;
use App\Models\ExperienceTask;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExperienceTasksSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'test@example.com')->first();
        $adminId = $admin?->id;

        $tasks = [
            [
                'title' => 'Build a REST API with Laravel',
                'description' => 'Create a fully functional REST API using Laravel. The API should include CRUD operations for a resource of your choice, authentication using Laravel Sanctum, and proper error handling with appropriate HTTP status codes.',
                'requirements' => "- Laravel 11+ project setup\n- At least 5 API endpoints (index, show, store, update, destroy)\n- Authentication with Sanctum\n- Request validation\n- API resource transformers\n- Proper error responses",
                'rewards' => 'Certificate of completion, featured on developer profile',
                'required_developers_count' => 5,
                'status' => ExperienceTaskStatus::OPEN,
                'price' => 0,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::THIRTY,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Create a Responsive Landing Page',
                'description' => 'Design and develop a modern, responsive landing page for a tech startup. The page should be visually appealing, mobile-first, and follow current web design trends. Use Tailwind CSS for styling.',
                'requirements' => "- Mobile-first responsive design\n- Tailwind CSS for styling\n- Smooth scroll animations\n- Contact form with validation\n- Dark mode support\n- Lighthouse score above 90",
                'rewards' => 'Portfolio showcase opportunity',
                'required_developers_count' => 8,
                'status' => ExperienceTaskStatus::OPEN,
                'price' => null,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::TWENTY,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Implement Real-time Chat Application',
                'description' => 'Build a real-time chat application using WebSockets. Users should be able to create rooms, send messages, and see who is online. The application should handle message persistence and typing indicators.',
                'requirements' => "- WebSocket implementation (Laravel Reverb or Pusher)\n- User authentication\n- Chat rooms with join/leave functionality\n- Message persistence in database\n- Typing indicators\n- Online status display\n- Message read receipts",
                'rewards' => 'Certificate of completion, 50,000 IQD bonus',
                'required_developers_count' => 3,
                'status' => ExperienceTaskStatus::IN_PROGRESS,
                'price' => 50000,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::FIFTY,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Build an E-commerce Product Catalog',
                'description' => 'Create a product catalog system with categories, filters, search functionality, and a shopping cart. Focus on clean architecture and reusable components.',
                'requirements' => "- Product listing with pagination\n- Category-based filtering\n- Full-text search\n- Shopping cart (session-based)\n- Product detail pages\n- Image gallery for products",
                'rewards' => 'Featured project badge, mentorship session',
                'required_developers_count' => 4,
                'status' => ExperienceTaskStatus::OPEN,
                'price' => 25000,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::FOURTY,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Write Unit Tests for a Laravel App',
                'description' => 'Write comprehensive unit and feature tests for an existing Laravel application. Cover models, controllers, middleware, and API endpoints. Achieve at least 80% code coverage.',
                'requirements' => "- PHPUnit or Pest test suite\n- Model tests with factory states\n- Feature tests for HTTP endpoints\n- Authentication flow tests\n- Database assertions\n- At least 80% code coverage",
                'rewards' => 'Quality Assurance badge',
                'required_developers_count' => 6,
                'status' => ExperienceTaskStatus::OPEN,
                'price' => null,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::TWENTY_FIVE,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Create a Blog Platform with CMS',
                'description' => 'Build a full-featured blog platform with an admin CMS panel. Include post creation with a rich text editor, categories, tags, comments, and SEO optimization features.',
                'requirements' => "- Rich text editor (TipTap or similar)\n- Post categories and tags\n- Comment system with moderation\n- SEO meta fields\n- RSS feed generation\n- Admin dashboard with analytics",
                'rewards' => 'Certificate, portfolio feature, 75,000 IQD',
                'required_developers_count' => 3,
                'status' => ExperienceTaskStatus::IN_PROGRESS,
                'price' => 75000,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::SIXTY,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Build a Task Management Dashboard',
                'description' => 'Create a Kanban-style task management dashboard. Users should be able to create boards, add tasks, drag and drop between columns, and assign tasks to team members.',
                'requirements' => "- Kanban board with drag-and-drop\n- Task creation and editing\n- Column management (To Do, In Progress, Done)\n- Task assignment to users\n- Due dates and priority levels\n- Task filtering and search",
                'rewards' => 'Problem Solver badge',
                'required_developers_count' => 5,
                'status' => ExperienceTaskStatus::OPEN,
                'price' => null,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::THIRTY_FIVE,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Implement OAuth Social Login',
                'description' => 'Add social authentication (Google, GitHub, LinkedIn) to an existing Laravel application using Laravel Socialite. Handle account linking for users who sign up with multiple providers.',
                'requirements' => "- Google OAuth integration\n- GitHub OAuth integration\n- LinkedIn OAuth integration\n- Account linking for existing users\n- Profile data sync from providers\n- Secure token storage",
                'rewards' => 'Security Expert recognition',
                'required_developers_count' => 4,
                'status' => ExperienceTaskStatus::OPEN,
                'price' => 15000,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::TWENTY,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Design a Database Schema for SaaS App',
                'description' => 'Design and implement a multi-tenant database schema for a SaaS application. Include tenant isolation, subscription management tables, and proper indexing for performance.',
                'requirements' => "- Multi-tenant schema design\n- Subscription and billing tables\n- User roles and permissions per tenant\n- Proper foreign keys and indexes\n- Migration files with seeders\n- ER diagram documentation",
                'rewards' => 'Architecture badge, mentorship opportunity',
                'required_developers_count' => 2,
                'status' => ExperienceTaskStatus::COMPLETED,
                'price' => 100000,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::FOURTY_FIVE,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Build a File Upload Microservice',
                'description' => 'Create a standalone file upload microservice that handles image optimization, virus scanning, and storage to cloud providers (S3-compatible). Include a clean API for other services to consume.',
                'requirements' => "- File upload API with validation\n- Image optimization and thumbnail generation\n- Support for S3-compatible storage\n- File type and size validation\n- Upload progress tracking\n- Secure file access with signed URLs",
                'rewards' => 'Microservice Expert badge',
                'required_developers_count' => 3,
                'status' => ExperienceTaskStatus::OPEN,
                'price' => null,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::THIRTY,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Create an API Documentation Site',
                'description' => 'Build an interactive API documentation site using OpenAPI/Swagger. Include code examples in multiple languages, authentication guides, and a live API playground.',
                'requirements' => "- OpenAPI 3.0 specification\n- Interactive API playground\n- Code examples in PHP, JavaScript, Python\n- Authentication documentation\n- Rate limiting documentation\n- Versioning support",
                'rewards' => 'Documentation Pro badge',
                'required_developers_count' => 2,
                'status' => ExperienceTaskStatus::OPEN,
                'price' => null,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::FIFTEEN,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Implement CI/CD Pipeline',
                'description' => 'Set up a complete CI/CD pipeline using GitHub Actions for a Laravel application. Include automated testing, code quality checks, staging deployment, and production deployment with zero downtime.',
                'requirements' => "- GitHub Actions workflow configuration\n- Automated test suite execution\n- Code quality checks (PHPStan, Pint)\n- Staging environment deployment\n- Production deployment with zero downtime\n- Slack/Discord notifications",
                'rewards' => 'DevOps badge, 60,000 IQD',
                'required_developers_count' => 2,
                'status' => ExperienceTaskStatus::IN_PROGRESS,
                'price' => 60000,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::FIFTY_FIVE,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Contribute to Open Source Laravel Package',
                'description' => 'Find and contribute meaningful improvements to an open-source Laravel package. This could include bug fixes, new features, documentation improvements, or test coverage. Submit at least 2 merged PRs.',
                'requirements' => "- At least 2 merged pull requests\n- Code follows package coding standards\n- Includes tests for new features/fixes\n- Documentation updated\n- PR descriptions are clear and detailed",
                'rewards' => 'Open Source Contributor badge, community recognition',
                'required_developers_count' => 10,
                'status' => ExperienceTaskStatus::OPEN,
                'price' => null,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::SEVENTY,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Build a Payment Integration System',
                'description' => 'Integrate a payment gateway (Stripe or similar) into a Laravel application. Handle subscriptions, one-time payments, refunds, and webhook processing with proper error handling.',
                'requirements' => "- Payment gateway integration\n- Subscription management\n- One-time payment processing\n- Refund handling\n- Webhook endpoint with verification\n- Invoice generation\n- Payment failure retry logic",
                'rewards' => 'Full Stack Expert badge, 100,000 IQD',
                'required_developers_count' => 3,
                'status' => ExperienceTaskStatus::OPEN,
                'price' => 100000,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::SIXTY_FIVE,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Optimize Database Performance',
                'description' => 'Analyze and optimize slow database queries in an existing Laravel application. Use query profiling, add proper indexes, implement caching strategies, and reduce N+1 query problems.',
                'requirements' => "- Query profiling with Laravel Debugbar\n- Index optimization\n- N+1 query elimination\n- Redis/cache implementation\n- Query result benchmarks (before/after)\n- Documentation of changes",
                'rewards' => 'Performance Expert badge',
                'required_developers_count' => 3,
                'status' => ExperienceTaskStatus::OPEN,
                'price' => null,
                'price_currency' => Currency::IQD,
                'experience_gain' => ExperienceGain::THIRTY_FIVE,
                'created_by' => $adminId,
            ],
        ];

        foreach ($tasks as $taskData) {
            $taskData['slug'] = Str::slug($taskData['title']);
            ExperienceTask::create($taskData);
        }

        // Assign some developers to in-progress and completed tasks
        $developers = Developer::all();
        if ($developers->isNotEmpty()) {
            $assignableTasks = ExperienceTask::whereIn('status', [
                ExperienceTaskStatus::IN_PROGRESS,
                ExperienceTaskStatus::COMPLETED,
            ])->get();

            foreach ($assignableTasks as $task) {
                $count = min($developers->count(), rand(1, $task->required_developers_count));
                $randomDevs = $developers->random($count);
                $task->developers()->attach($randomDevs->pluck('id'));
            }
        }
    }
}
