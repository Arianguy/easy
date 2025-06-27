<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $types = ['email', 'sms', 'social_media', 'print', 'event', 'digital_ads'];
        $statuses = ['draft', 'active', 'paused', 'completed', 'cancelled'];

        foreach ($branches as $branch) {
            $users = User::where('branch_id', $branch->id)->get();

            for ($i = 1; $i <= 5; $i++) {
                $status = $statuses[array_rand($statuses)];
                $startDate = now()->subDays(rand(1, 90));
                $endDate = $startDate->copy()->addDays(rand(7, 60));

                Campaign::create([
                    'name' => 'Campaign ' . $i . ' - ' . $branch->name,
                    'description' => 'Sample campaign description for ' . $branch->name,
                    'type' => $types[array_rand($types)],
                    'status' => $status,
                    'start_date' => $startDate,
                    'end_date' => $status === 'completed' ? $endDate : ($status === 'active' ? null : $endDate),
                    'budget' => rand(1000, 10000),
                    'actual_cost' => in_array($status, ['completed', 'cancelled']) ? rand(500, 8000) : rand(0, 5000),
                    'target_audience' => rand(100, 1000),
                    'reached_audience' => rand(50, 800),
                    'leads_generated' => rand(5, 50),
                    'conversions' => rand(1, 20),
                    'metrics' => json_encode([
                        'clicks' => rand(100, 1000),
                        'impressions' => rand(1000, 10000),
                        'engagement_rate' => rand(1, 15),
                    ]),
                    'branch_id' => $branch->id,
                    'created_by' => $users->random()->id,
                ]);
            }
        }
    }
}
