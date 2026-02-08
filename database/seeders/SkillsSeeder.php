<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Programming Languages
            'PHP', 'JavaScript', 'TypeScript', 'Python', 'Java', 'C#', 'Ruby', 'Go', 'Rust', 'Swift', 'Kotlin',

            // Frontend Frameworks
            'React', 'Vue.js', 'Angular', 'Svelte', 'Next.js', 'Nuxt.js',

            // Backend Frameworks
            'Laravel', 'Symfony', 'Django', 'Flask', 'Node.js', 'Express.js', 'NestJS', 'Spring Boot', 'ASP.NET Core', 'Ruby on Rails',

            // Mobile Development
            'React Native', 'Flutter', 'iOS Development', 'Android Development',

            // Databases
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite', 'Oracle', 'Microsoft SQL Server', 'Elasticsearch',

            // DevOps & Cloud
            'Docker', 'Kubernetes', 'AWS', 'Azure', 'Google Cloud Platform', 'Jenkins', 'GitLab CI/CD', 'GitHub Actions', 'Terraform', 'Ansible',

            // CSS & Styling
            'CSS', 'Sass/SCSS', 'Tailwind CSS', 'Bootstrap', 'Material UI',

            // Tools & Others
            'Git', 'GitHub', 'GitLab', 'REST API', 'GraphQL', 'Webpack', 'Vite', 'npm', 'Composer', 'Linux', 'Nginx', 'Apache',

            // Testing
            'PHPUnit', 'Jest', 'Pytest', 'Cypress',

            // Design
            'Figma', 'Adobe XD', 'Sketch', 'Photoshop',

            // Other Important Skills
            'Agile/Scrum', 'Microservices', 'RESTful Services', 'CI/CD', 'Unit Testing', 'TDD',
        ];

        foreach ($skills as $skillName) {
            Skill::create([
                'name' => $skillName,
                'slug' => Str::slug($skillName),
            ]);
        }
    }
}
