<?php

namespace Database\Seeders;

use App\Enums\DeveloperStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\WorldGovernorate;
use App\Models\Developer;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DevelopersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Sample Developers with their skills
        $developers = [
            [
                'data' => [
                    'name' => 'John Smith',
                    'email' => 'john.smith@example.com',
                    'phone' => '+1-555-0101',
                    'job_title_id' => 1,
                    'years_of_experience' => 5,
                    'bio' => 'Experienced full-stack developer with a passion for building scalable web applications. Proficient in modern JavaScript frameworks and cloud technologies.',
                    'portfolio_url' => 'https://johnsmith.dev',
                    'github_url' => 'https://github.com/johnsmith',
                    'linkedin_url' => 'https://linkedin.com/in/johnsmith',
                    'location' => WorldGovernorate::BAGHDAD,
                    'expected_salary_from' => 195000000,
                    'expected_salary_to' => 234000000,
                    'is_available' => true,
                    'status' => DeveloperStatus::APPROVED,
                    'subscription_plan' => SubscriptionPlan::PREMIUM,
                ],
                'skills' => ['Laravel', 'Vue.js', 'React', 'Node.js', 'MySQL', 'AWS'],
            ],
            [
                'data' => [
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah.johnson@example.com',
                    'phone' => '+1-555-0102',
                    'job_title_id' => 2,
                    'years_of_experience' => 3,
                    'bio' => 'Creative frontend developer focused on creating beautiful and responsive user interfaces. Expert in modern CSS frameworks and JavaScript.',
                    'portfolio_url' => 'https://sarahjohnson.com',
                    'github_url' => 'https://github.com/sarahjohnson',
                    'linkedin_url' => 'https://linkedin.com/in/sarahjohnson',
                    'location' => WorldGovernorate::ERBIL,
                    'expected_salary_from' => 169000000,
                    'expected_salary_to' => 208000000,
                    'is_available' => true,
                    'subscription_plan' => SubscriptionPlan::PRO,
                ],
                'skills' => ['React', 'TypeScript', 'Tailwind CSS', 'Next.js', 'Figma'],
            ],
            [
                'data' => [
                    'name' => 'Michael Chen',
                    'email' => 'michael.chen@example.com',
                    'phone' => '+1-555-0103',
                    'job_title_id' => 3,
                    'years_of_experience' => 7,
                    'bio' => 'Senior backend developer with extensive experience in API design and database optimization. Specialized in microservices architecture.',
                    'portfolio_url' => 'https://michaelchen.dev',
                    'github_url' => 'https://github.com/michaelchen',
                    'linkedin_url' => 'https://linkedin.com/in/michaelchen',
                    'location' => WorldGovernorate::BASRA,
                    'expected_salary_from' => 221000000,
                    'expected_salary_to' => 260000000,
                    'is_available' => true,
                    'subscription_plan' => SubscriptionPlan::PREMIUM,
                    'recommended_by_us' => true,
                ],
                'skills' => ['PHP', 'Laravel', 'PostgreSQL', 'Redis', 'Docker', 'Kubernetes'],
            ],
            [
                'data' => [
                    'name' => 'Emily Rodriguez',
                    'email' => 'emily.rodriguez@example.com',
                    'phone' => '+1-555-0104',
                    'job_title_id' => 4,
                    'years_of_experience' => 4,
                    'bio' => 'Mobile app developer with a focus on cross-platform solutions. Published multiple apps with 100K+ downloads.',
                    'portfolio_url' => 'https://emilyrodriguez.dev',
                    'github_url' => 'https://github.com/emilyrodriguez',
                    'linkedin_url' => 'https://linkedin.com/in/emilyrodriguez',
                    'location' => WorldGovernorate::SULAYMANIYAH,
                    'expected_salary_from' => 182000000,
                    'expected_salary_to' => 221000000,
                    'is_available' => false,
                    'subscription_plan' => SubscriptionPlan::PRO,
                ],
                'skills' => ['React Native', 'Flutter', 'iOS Development', 'Android Development'],
            ],
            [
                'data' => [
                    'name' => 'David Lee',
                    'email' => 'david.lee@example.com',
                    'phone' => '+1-555-0105',
                    'job_title_id' => 5,
                    'years_of_experience' => 6,
                    'bio' => 'DevOps engineer specializing in CI/CD pipelines and cloud infrastructure. Expert in automating deployment processes.',
                    'portfolio_url' => 'https://davidlee.tech',
                    'github_url' => 'https://github.com/davidlee',
                    'linkedin_url' => 'https://linkedin.com/in/davidlee',
                    'location' => WorldGovernorate::NAJAF,
                    'expected_salary_from' => 208000000,
                    'expected_salary_to' => 247000000,
                    'is_available' => true,
                    'status' => DeveloperStatus::APPROVED,
                ],
                'skills' => ['AWS', 'Docker', 'Kubernetes', 'Jenkins', 'Terraform', 'Ansible'],
            ],
            [
                'data' => [
                    'name' => 'Jessica Brown',
                    'email' => 'jessica.brown@example.com',
                    'phone' => '+1-555-0106',
                    'job_title_id' => 6,
                    'years_of_experience' => 5,
                    'bio' => 'UI/UX designer passionate about creating intuitive and accessible user experiences. Award-winning portfolio.',
                    'portfolio_url' => 'https://jessicabrown.design',
                    'github_url' => 'https://github.com/jessicabrown',
                    'linkedin_url' => 'https://linkedin.com/in/jessicabrown',
                    'location' => WorldGovernorate::KARBALA,
                    'expected_salary_from' => 156000000,
                    'expected_salary_to' => 195000000,
                    'is_available' => true,
                    'status' => DeveloperStatus::APPROVED,
                ],
                'skills' => ['Figma', 'Adobe XD', 'Sketch'],
            ],
            [
                'data' => [
                    'name' => 'Robert Martinez',
                    'email' => 'robert.martinez@example.com',
                    'phone' => '+1-555-0107',
                    'job_title_id' => 1,
                    'years_of_experience' => 2,
                    'bio' => 'Junior full-stack developer eager to learn and grow. Recently completed a coding bootcamp and ready to contribute.',
                    'portfolio_url' => 'https://robertmartinez.dev',
                    'github_url' => 'https://github.com/robertmartinez',
                    'linkedin_url' => 'https://linkedin.com/in/robertmartinez',
                    'location' => WorldGovernorate::NINEVEH,
                    'expected_salary_from' => 104000000,
                    'expected_salary_to' => 130000000,
                    'is_available' => true,
                    'status' => DeveloperStatus::APPROVED,
                ],
                'skills' => ['JavaScript', 'Python', 'Django', 'React', 'MongoDB'],
            ],
            [
                'data' => [
                    'name' => 'Amanda Taylor',
                    'email' => 'amanda.taylor@example.com',
                    'phone' => '+1-555-0108',
                    'job_title_id' => 3,
                    'years_of_experience' => 10,
                    'bio' => 'Senior backend architect with a decade of experience. Led multiple high-traffic projects and mentored junior developers.',
                    'portfolio_url' => 'https://amandataylor.dev',
                    'github_url' => 'https://github.com/amandataylor',
                    'linkedin_url' => 'https://linkedin.com/in/amandataylor',
                    'location' => WorldGovernorate::KIRKUK,
                    'expected_salary_from' => 260000000,
                    'expected_salary_to' => 325000000,
                    'is_available' => true,
                    'recommended_by_us' => true,
                    'status' => DeveloperStatus::APPROVED,
                ],
                'skills' => ['Java', 'Spring Boot', 'Microservices', 'PostgreSQL', 'Elasticsearch'],
            ],
        ];

        foreach ($developers as $developerInfo) {
            // Generate slug from name
            $developerInfo['data']['slug'] = Str::slug($developerInfo['data']['name']);

            // Create the developer
            $developer = Developer::create($developerInfo['data']);

            // Attach skills
            $skillNames = $developerInfo['skills'];
            $skillIds = Skill::whereIn('name', $skillNames)->pluck('id')->toArray();

            $developer->skills()->attach($skillIds);
        }
    }
}
