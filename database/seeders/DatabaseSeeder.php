<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Portfolio;
use App\Models\Project;
use App\Models\SocialLink;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@gradfolio.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Sample graduates
        $graduates = [
            [
                'name'     => 'Sarah Al-Hassan',
                'email'    => 'sarah@example.com',
                'headline' => 'Full-Stack Web Developer',
                'bio'      => 'Passionate developer with expertise in Laravel, Vue.js and cloud infrastructure. Graduated 2024.',
                'location' => 'Dubai, UAE',
            ],
            [
                'name'     => 'Omar Khalil',
                'email'    => 'omar@example.com',
                'headline' => 'UI/UX Designer & Front-End Developer',
                'bio'      => 'I craft beautiful, user-centered digital experiences. Specializing in React and Figma.',
                'location' => 'Cairo, Egypt',
            ],
            [
                'name'     => 'Lina Mansour',
                'email'    => 'lina@example.com',
                'headline' => 'Data Scientist & ML Engineer',
                'bio'      => 'Turning raw data into actionable insights using Python, TensorFlow, and modern ML techniques.',
                'location' => 'Beirut, Lebanon',
            ],
        ];

        foreach ($graduates as $graduateData) {
            $user = User::create([
                'name'     => $graduateData['name'],
                'email'    => $graduateData['email'],
                'password' => Hash::make('password'),
                'role'     => 'graduate',
            ]);

            $portfolio = Portfolio::create([
                'user_id'      => $user->id,
                'title'        => $user->name,
                'headline'     => $graduateData['headline'],
                'bio'          => $graduateData['bio'],
                'location'     => $graduateData['location'],
                'is_published' => true,
                'slug'         => \Illuminate\Support\Str::slug($user->name) . '-' . $user->id,
            ]);

            SocialLink::create(['portfolio_id' => $portfolio->id, 'platform' => 'github', 'url' => 'https://github.com']);
            SocialLink::create(['portfolio_id' => $portfolio->id, 'platform' => 'linkedin', 'url' => 'https://linkedin.com']);

            Project::create([
                'portfolio_id' => $portfolio->id,
                'title'        => 'Featured Project Alpha',
                'description'  => 'A full-featured web application built with modern technologies, focusing on performance and user experience.',
                'tech_stack'   => 'Laravel, Vue.js, MySQL, Tailwind CSS',
                'project_url'  => 'https://example.com',
                'github_url'   => 'https://github.com',
                'sort_order'   => 1,
            ]);

            Project::create([
                'portfolio_id' => $portfolio->id,
                'title'        => 'Open Source Contribution',
                'description'  => 'Contributed a significant feature to a popular open-source project with over 10k stars on GitHub.',
                'tech_stack'   => 'Python, Docker, REST API',
                'github_url'   => 'https://github.com',
                'sort_order'   => 2,
            ]);
        }
    }
}
