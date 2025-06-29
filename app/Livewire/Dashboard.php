<?php

namespace App\Livewire;

use App\Models\Activity;
use App\Models\Campaign;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $selectedPeriod = '30'; // days
    public $selectedBranch = null;

    public function mount()
    {
        if (!auth()->user()?->canManageAllBranches()) {
            $this->selectedBranch = auth()->user()?->branch_id;
        }
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'stats' => $this->getStats(),
            'chartData' => $this->getChartData(),
            'recentActivities' => $this->getRecentActivities(),
            'upcomingActivities' => $this->getUpcomingActivities(),
            'topPerformers' => $this->getTopPerformers(),
            'overdueLeads' => $this->getOverdueLeads(),
        ]);
    }

    private function getStats()
    {
        $query = $this->getBaseQuery();
        $period = now()->subDays($this->selectedPeriod);

        return [
            'total_customers' => $this->getCustomerQuery()->count(),
            'new_customers' => $this->getCustomerQuery()->where('created_at', '>=', $period)->count(),
            'total_leads' => $this->getLeadQuery()->count(),
            'new_leads' => $this->getLeadQuery()->where('created_at', '>=', $period)->count(),
            'active_leads' => $this->getLeadQuery()->whereIn('status', ['new', 'contacted', 'qualified'])->count(),
            'converted_leads' => $this->getLeadQuery()->where('status', 'converted')->count(),
            'total_opportunities' => $this->getOpportunityQuery()->count(),
            'active_opportunities' => $this->getOpportunityQuery()->whereNotIn('stage', ['won', 'lost'])->count(),
            'won_opportunities' => $this->getOpportunityQuery()->where('stage', 'won')->count(),
            'total_revenue' => $this->getOpportunityQuery()->where('stage', 'won')->sum('value'),
            'pipeline_value' => $this->getOpportunityQuery()->whereNotIn('stage', ['won', 'lost'])->sum('value'),
            'weighted_pipeline' => $this->getWeightedPipelineValue(),
            'conversion_rate' => $this->getConversionRate(),
            'avg_deal_size' => $this->getAverageDealSize(),
            'activities_completed' => $this->getActivityQuery()->where('status', 'completed')->where('completed_at', '>=', $period)->count(),
            'activities_pending' => $this->getActivityQuery()->where('status', 'pending')->count(),
            'overdue_activities' => $this->getActivityQuery()->where('scheduled_at', '<', now())->where('status', 'pending')->count(),
        ];
    }

    private function getChartData()
    {
        return [
            'leads_trend' => $this->getLeadsTrend(),
            'revenue_trend' => $this->getRevenueTrend(),
            'lead_sources' => $this->getLeadSources(),
            'opportunity_stages' => $this->getOpportunityStages(),
            'conversion_funnel' => $this->getConversionFunnel(),
            'team_performance' => $this->getTeamPerformance(),
        ];
    }

    private function getLeadsTrend()
    {
        $days = collect(range(0, 29))->map(function ($i) {
            $date = now()->subDays(29 - $i);
            return [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('M j'),
                'new_leads' => $this->getLeadQuery()
                    ->whereDate('created_at', $date)
                    ->count(),
                'converted_leads' => $this->getLeadQuery()
                    ->whereDate('updated_at', $date)
                    ->where('status', 'converted')
                    ->count(),
            ];
        });

        return $days;
    }

    private function getRevenueTrend()
    {
        $months = collect(range(0, 11))->map(function ($i) {
            $date = now()->subMonths(11 - $i);
            return [
                'month' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
                'revenue' => $this->getOpportunityQuery()
                    ->where('stage', 'won')
                    ->whereYear('actual_close_date', $date->year)
                    ->whereMonth('actual_close_date', $date->month)
                    ->sum('value'),
            ];
        });

        return $months;
    }

    private function getLeadSources()
    {
        return $this->getLeadQuery()
            ->select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucfirst(str_replace('_', ' ', $item->source)),
                    'value' => $item->count,
                ];
            });
    }

    private function getOpportunityStages()
    {
        return $this->getOpportunityQuery()
            ->select('stage', DB::raw('count(*) as count'), DB::raw('sum(value) as total_value'))
            ->groupBy('stage')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucfirst($item->stage),
                    'count' => $item->count,
                    'value' => $item->total_value,
                ];
            });
    }

    private function getConversionFunnel()
    {
        $totalLeads = $this->getLeadQuery()->count();
        $contactedLeads = $this->getLeadQuery()->whereIn('status', ['contacted', 'qualified', 'converted'])->count();
        $qualifiedLeads = $this->getLeadQuery()->whereIn('status', ['qualified', 'converted'])->count();
        $convertedLeads = $this->getLeadQuery()->where('status', 'converted')->count();

        return [
            ['stage' => 'Total Leads', 'count' => $totalLeads, 'percentage' => 100],
            ['stage' => 'Contacted', 'count' => $contactedLeads, 'percentage' => $totalLeads > 0 ? round(($contactedLeads / $totalLeads) * 100, 1) : 0],
            ['stage' => 'Qualified', 'count' => $qualifiedLeads, 'percentage' => $totalLeads > 0 ? round(($qualifiedLeads / $totalLeads) * 100, 1) : 0],
            ['stage' => 'Converted', 'count' => $convertedLeads, 'percentage' => $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0],
        ];
    }

    private function getTeamPerformance()
    {
        $query = User::query();

        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        return $query->with(['assignedLeads'])
            ->get()
            ->map(function ($user) {
                $totalLeads = $user->assignedLeads->count();
                $convertedLeads = $user->assignedLeads->where('status', 'converted')->count();

                return [
                    'name' => $user->name,
                    'total_leads' => $totalLeads,
                    'converted_leads' => $convertedLeads,
                    'conversion_rate' => $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('conversion_rate')
            ->take(10);
    }

    private function getRecentActivities()
    {
        return $this->getActivityQuery()
            ->with(['user', 'related'])
            ->latest()
            ->take(10)
            ->get();
    }

    private function getUpcomingActivities()
    {
        return $this->getActivityQuery()
            ->where('scheduled_at', '>=', now())
            ->where('status', 'pending')
            ->with(['user', 'related'])
            ->orderBy('scheduled_at')
            ->take(10)
            ->get();
    }

    private function getTopPerformers()
    {
        $query = User::query();

        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        return $query->withCount(['assignedLeads as total_leads', 'assignedLeads as converted_leads' => function ($q) {
            $q->where('status', 'converted');
        }])
            ->having('total_leads', '>', 0)
            ->get()
            ->map(function ($user) {
                // Use the model's existing accessor instead of modifying the property
                return $user;
            })
            ->sortByDesc('conversion_rate')
            ->take(5);
    }

    private function getOverdueLeads()
    {
        return $this->getLeadQuery()
            ->where('follow_up_date', '<', now())
            ->whereNotIn('status', ['converted', 'lost'])
            ->with(['customer', 'assignedUser'])
            ->orderBy('follow_up_date')
            ->take(10)
            ->get();
    }

    private function getWeightedPipelineValue()
    {
        return $this->getOpportunityQuery()
            ->whereNotIn('stage', ['won', 'lost'])
            ->get()
            ->sum(function ($opportunity) {
                return ($opportunity->value * $opportunity->probability) / 100;
            });
    }

    private function getConversionRate()
    {
        $totalLeads = $this->getLeadQuery()->count();
        $convertedLeads = $this->getLeadQuery()->where('status', 'converted')->count();

        return $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;
    }

    private function getAverageDealSize()
    {
        return $this->getOpportunityQuery()
            ->where('stage', 'won')
            ->avg('value') ?: 0;
    }

    private function getBaseQuery()
    {
        return $this->selectedBranch ? ['branch_id' => $this->selectedBranch] : [];
    }

    private function getCustomerQuery()
    {
        $query = Customer::query();
        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }
        return $query;
    }

    private function getLeadQuery()
    {
        $query = Lead::query();
        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }
        return $query;
    }

    private function getOpportunityQuery()
    {
        $query = Opportunity::query();
        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }
        return $query;
    }

    private function getActivityQuery()
    {
        $query = Activity::query();
        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }
        return $query;
    }
}
