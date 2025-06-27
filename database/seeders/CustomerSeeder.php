<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $sources = ['walk_in', 'referral', 'online', 'campaign', 'cold_call'];
        $statuses = ['active', 'inactive', 'potential'];

        foreach ($branches as $branch) {
            $users = User::where('branch_id', $branch->id)->get();

            for ($i = 1; $i <= 20; $i++) {
                Customer::create([
                    'name' => 'Customer ' . $i . ' - ' . $branch->name,
                    'phone' => '+1-555-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'mobile' => '+1-555-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'email' => 'customer' . $i . '@' . strtolower(str_replace(' ', '', $branch->name)) . '.com',
                    'interests' => collect(['Electronics', 'Home Appliances', 'Fashion', 'Sports', 'Books', 'Travel'])->random(rand(1, 3))->implode(', '),
                    'address' => rand(100, 999) . ' Street Name, ' . $branch->location,
                    'company' => rand(0, 1) ? 'Company ' . rand(1, 50) . ' Inc.' : null,
                    'budget_range' => rand(1000, 50000),
                    'source' => $sources[array_rand($sources)],
                    'status' => $statuses[array_rand($statuses)],
                    'last_contact_date' => rand(0, 1) ? now()->subDays(rand(1, 90)) : null,
                    'notes' => 'Sample notes for customer ' . $i,
                    'branch_id' => $branch->id,
                    'created_by' => $users->random()->id,
                ]);
            }
        }
    }
}
