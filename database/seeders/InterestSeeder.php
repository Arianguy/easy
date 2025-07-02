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
                'name' => 'Smartphones',
                'description' => 'All Smartphones',
                'color' => '#3B82F6',
                'sort_order' => 1,
            ],
            [
                'name' => 'Tablets',
                'description' => 'All Tablets',
                'color' => '#10B981',
                'sort_order' => 2,
            ],
            [
                'name' => 'Phone Accessories',
                'description' => 'All Mobile Accessories',
                'color' => '#F59E0B',
                'sort_order' => 3,
            ],
            [
                'name' => 'Laptops & PCs',
                'description' => 'All Laptops & PCs',
                'color' => '#8B5CF6',
                'sort_order' => 4,
            ],
            [
                'name' => 'Electronics',
                'description' => 'All Electronics',
                'color' => '#06B6D4',
                'sort_order' => 5,
            ],
            [
                'name' => 'Smartwatches',
                'description' => 'All Smartwatches',
                'color' => '#84CC16',
                'sort_order' => 6,
            ],
            [
                'name' => 'Smart Home',
                'description' => 'All Smart Home',
                'color' => '#84CC16',
                'sort_order' => 7,
            ],
        ];

        foreach ($interests as $interest) {
            Interest::create($interest);
        }
    }
}
