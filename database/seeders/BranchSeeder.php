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
            ],
            [
                'name' => 'Uptown Branch',
                'location' => 'Uptown District',
                'email' => 'uptown@company.com',
                'phone_no' => '+1-555-0102',
                'address' => '456 Oak Avenue, Uptown, City 12346',
                'manager_name' => 'Sarah Johnson',
                'is_active' => true,
            ],
            [
                'name' => 'Westside Branch',
                'location' => 'West District',
                'email' => 'westside@company.com',
                'phone_no' => '+1-555-0103',
                'address' => '789 Pine Road, Westside, City 12347',
                'manager_name' => 'Michael Brown',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
