<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $types = ['call', 'email', 'meeting', 'note', 'task'];
        $statuses = ['pending', 'completed', 'cancelled'];
        $outcomes = ['successful', 'unsuccessful', 'rescheduled', 'no_response'];

        foreach ($branches as $branch) {
            $users = User::where('branch_id', $branch->id)->get();
            $customers = Customer::where('branch_id', $branch->id)->get();
            $leads = Lead::where('branch_id', $branch->id)->get();
            $opportunities = Opportunity::where('branch_id', $branch->id)->get();

            // Create activities for customers
            foreach ($customers->take(10) as $customer) {
                for ($i = 1; $i <= rand(1, 5); $i++) {
                    $status = $statuses[array_rand($statuses)];
                    $type = $types[array_rand($types)];

                    Activity::create([
                        'type' => $type,
                        'subject' => ucfirst($type) . ' with ' . $customer->name,
                        'description' => 'Sample ' . $type . ' activity with customer ' . $customer->name,
                        'status' => $status,
                        'scheduled_at' => now()->subDays(rand(1, 30))->addHours(rand(9, 17)),
                        'completed_at' => $status === 'completed' ? now()->subDays(rand(1, 20)) : null,
                        'duration_minutes' => $status === 'completed' ? rand(15, 120) : null,
                        'outcome' => $status === 'completed' ? $outcomes[array_rand($outcomes)] : null,
                        'related_type' => Customer::class,
                        'related_id' => $customer->id,
                        'user_id' => $users->random()->id,
                        'branch_id' => $branch->id,
                    ]);
                }
            }

            // Create activities for leads
            foreach ($leads->take(15) as $lead) {
                for ($i = 1; $i <= rand(1, 3); $i++) {
                    $status = $statuses[array_rand($statuses)];
                    $type = $types[array_rand($types)];

                    Activity::create([
                        'type' => $type,
                        'subject' => ucfirst($type) . ' for lead: ' . $lead->title,
                        'description' => 'Sample ' . $type . ' activity for lead ' . $lead->title,
                        'status' => $status,
                        'scheduled_at' => now()->subDays(rand(1, 30))->addHours(rand(9, 17)),
                        'completed_at' => $status === 'completed' ? now()->subDays(rand(1, 20)) : null,
                        'duration_minutes' => $status === 'completed' ? rand(15, 120) : null,
                        'outcome' => $status === 'completed' ? $outcomes[array_rand($outcomes)] : null,
                        'related_type' => Lead::class,
                        'related_id' => $lead->id,
                        'user_id' => $users->random()->id,
                        'branch_id' => $branch->id,
                    ]);
                }
            }

            // Create activities for opportunities
            foreach ($opportunities->take(10) as $opportunity) {
                for ($i = 1; $i <= rand(1, 2); $i++) {
                    $status = $statuses[array_rand($statuses)];
                    $type = $types[array_rand($types)];

                    Activity::create([
                        'type' => $type,
                        'subject' => ucfirst($type) . ' for opportunity: ' . $opportunity->name,
                        'description' => 'Sample ' . $type . ' activity for opportunity ' . $opportunity->name,
                        'status' => $status,
                        'scheduled_at' => now()->subDays(rand(1, 30))->addHours(rand(9, 17)),
                        'completed_at' => $status === 'completed' ? now()->subDays(rand(1, 20)) : null,
                        'duration_minutes' => $status === 'completed' ? rand(15, 120) : null,
                        'outcome' => $status === 'completed' ? $outcomes[array_rand($outcomes)] : null,
                        'related_type' => Opportunity::class,
                        'related_id' => $opportunity->id,
                        'user_id' => $users->random()->id,
                        'branch_id' => $branch->id,
                    ]);
                }
            }
        }
    }
}
