<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Calicut Branch',
                'location' => 'Calicut District',
                'email' => 'calicut@easystore.com',
                'phone_no' => '+91421235689',
                'address' => 'Calicut, Kerala, India',
                'manager_name' => 'Nishad LLM',
                'is_active' => true,
            ]
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
