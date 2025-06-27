<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Seeder;

class OpportunitySeeder extends Seeder
{
    public function run(): void
    {
        $convertedLeads = Lead::where('status', 'converted')->get();
        $stages = ['prospecting', 'proposal', 'negotiation', 'won', 'lost'];

        foreach ($convertedLeads as $lead) {
            $stage = $stages[array_rand($stages)];
            $probability = match ($stage) {
                'prospecting' => rand(10, 30),
                'proposal' => rand(25, 50),
                'negotiation' => rand(40, 80),
                'won' => 100,
                'lost' => 0,
            };

            Opportunity::create([
                'name' => 'Opportunity for ' . $lead->title,
                'value' => $lead->estimated_value ?: rand(1000, 50000),
                'stage' => $stage,
                'probability' => $probability,
                'expected_close_date' => $lead->expected_close_date ?: now()->addDays(rand(30, 90)),
                'actual_close_date' => in_array($stage, ['won', 'lost']) ? now()->subDays(rand(1, 30)) : null,
                'description' => 'Opportunity created from lead: ' . $lead->title,
                'close_reason' => in_array($stage, ['won', 'lost'])
                    ? ($stage === 'won' ? 'Customer satisfied with proposal' : 'Price too high')
                    : null,
                'products_services' => json_encode(['Product A', 'Service B']),
                'lead_id' => $lead->id,
                'branch_id' => $lead->branch_id,
                'created_by' => $lead->created_by,
            ]);
        }
    }
}
