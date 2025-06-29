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
                'name' => 'Downtown Branch',
                'location' => 'Downtown District',
                'email' => 'downtown@company.com',
                'phone_no' => '+1-555-0101',
                'address' => '123 Main Street, Downtown, City 12345',
                'manager_name' => 'John Smith',
                'is_active' => true,
            ]
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
