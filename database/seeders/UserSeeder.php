<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();

        // Create Area Manager (can access all branches)
        $areaManager = User::create([
            'name' => 'Area Manager',
            'email' => 'savio@ashtelgroup.com',
            'password' => Hash::make('Savio123'),
            'phone' => '+919822456789',
            'designation' => 'Area Manager',
            'is_active' => true,
            'branch_id' => null, // Area manager can access all branches
        ]);
        $areaManager->assignRole('Area Manager');

        // // Create Sales Managers for each branch
        // foreach ($branches as $index => $branch) {
        //     $manager = User::create([
        //         'name' => 'Sales Manager ' . ($index + 1),
        //         'email' => 'manager' . ($index + 1) . '@company.com',
        //         'password' => Hash::make('password'),
        //         'phone' => '+1-555-' . str_pad($index + 10, 4, '0', STR_PAD_LEFT),
        //         'designation' => 'Sales Manager',
        //         'is_active' => true,
        //         'branch_id' => $branch->id,
        //     ]);
        //     $manager->assignRole('Sales Manager');

        //     // Create Sales Executives for each branch
        //     for ($i = 1; $i <= 2; $i++) {
        //         $executive = User::create([
        //             'name' => 'Sales Executive ' . (($index * 2) + $i),
        //             'email' => 'sales' . (($index * 2) + $i) . '@company.com',
        //             'password' => Hash::make('password'),
        //             'phone' => '+1-555-' . str_pad((($index * 2) + $i) + 20, 4, '0', STR_PAD_LEFT),
        //             'designation' => 'Sales Executive',
        //             'is_active' => true,
        //             'branch_id' => $branch->id,
        //         ]);
        //         $executive->assignRole('Sales Executive');
        //     }
        // }
    }
}
