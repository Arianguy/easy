<?php

namespace Database\Seeders;

use App\Models\Interest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interests = [
            [
                'name' => 'Web Development',
                'description' => 'Website development and web applications',
                'color' => '#3B82F6',
                'sort_order' => 1,
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'iOS and Android mobile applications',
                'color' => '#10B981',
                'sort_order' => 2,
            ],
            [
                'name' => 'Digital Marketing',
                'description' => 'SEO, SEM, social media marketing',
                'color' => '#F59E0B',
                'sort_order' => 3,
            ],
            [
                'name' => 'E-commerce Solutions',
                'description' => 'Online store development and management',
                'color' => '#8B5CF6',
                'sort_order' => 4,
            ],
            [
                'name' => 'Cloud Services',
                'description' => 'Cloud hosting and infrastructure',
                'color' => '#06B6D4',
                'sort_order' => 5,
            ],
            [
                'name' => 'Software Consulting',
                'description' => 'Technical consulting and architecture',
                'color' => '#84CC16',
                'sort_order' => 6,
            ],
            [
                'name' => 'Data Analytics',
                'description' => 'Business intelligence and data analysis',
                'color' => '#EF4444',
                'sort_order' => 7,
            ],
            [
                'name' => 'Cybersecurity',
                'description' => 'Security audits and protection services',
                'color' => '#F97316',
                'sort_order' => 8,
            ],
            [
                'name' => 'UI/UX Design',
                'description' => 'User interface and experience design',
                'color' => '#EC4899',
                'sort_order' => 9,
            ],
            [
                'name' => 'Training & Support',
                'description' => 'Technical training and ongoing support',
                'color' => '#6366F1',
                'sort_order' => 10,
            ],
            [
                'name' => 'System Integration',
                'description' => 'Integration of different software systems',
                'color' => '#14B8A6',
                'sort_order' => 11,
            ],
            [
                'name' => 'AI & Machine Learning',
                'description' => 'Artificial intelligence and ML solutions',
                'color' => '#A855F7',
                'sort_order' => 12,
            ],
        ];

        foreach ($interests as $interest) {
            Interest::create($interest);
        }
    }
}
