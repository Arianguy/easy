<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $statuses = ['new', 'contacted', 'qualified', 'lost', 'converted'];
        $priorities = ['low', 'medium', 'high'];
        $sources = ['walk_in', 'referral', 'online', 'campaign', 'cold_call'];

        foreach ($branches as $branch) {
            $customers = Customer::where('branch_id', $branch->id)->get();
            $users = User::where('branch_id', $branch->id)->get();

            foreach ($customers as $customer) {
                // Create 1-3 leads per customer
                $leadCount = rand(1, 3);

                for ($i = 1; $i <= $leadCount; $i++) {
                    $status = $statuses[array_rand($statuses)];

                    Lead::create([
                        'title' => 'Lead ' . $i . ' for ' . $customer->name,
                        'description' => 'Sample lead description for ' . $customer->name . '. Interest in our products/services.',
                        'status' => $status,
                        'priority' => $priorities[array_rand($priorities)],
                        'source' => $sources[array_rand($sources)],
                        'follow_up_date' => in_array($status, ['new', 'contacted', 'qualified'])
                            ? now()->addDays(rand(1, 30))
                            : null,
                        'notes' => 'Sample notes for lead ' . $i,
                        'estimated_value' => rand(500, 25000),
                        'expected_close_date' => in_array($status, ['qualified', 'converted'])
                            ? now()->addDays(rand(30, 90))
                            : null,
                        'tags' => json_encode(['tag1', 'tag2']),
                        'customer_id' => $customer->id,
                        'assigned_user_id' => $users->random()->id,
                        'branch_id' => $branch->id,
                        'created_by' => $users->random()->id,
                    ]);
                }
            }
        }
    }
}
