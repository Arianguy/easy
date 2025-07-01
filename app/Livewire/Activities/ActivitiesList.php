<?php

namespace App\Livewire\Activities;

use App\Models\Activity;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ActivitiesList extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $statusFilter = '';
    public $outcomeFilter = '';
    public $dateFilter = '';
    public $relatedFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'outcomeFilter' => ['except' => ''],
        'dateFilter' => ['except' => ''],
        'relatedFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingOutcomeFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function updatingRelatedFilter()
    {
        $this->resetPage();
    }

    public function markAsCompleted($activityId, $outcome = 'successful')
    {
        $activity = Activity::findOrFail($activityId);

        // Check permissions
        $user = Auth::user();
        if (!$user->hasRole('Area Manager') && $activity->user_id !== $user->id) {
            session()->flash('error', 'You can only complete your own activities.');
            return;
        }

        $activity->markAsCompleted($outcome);
        session()->flash('message', 'Activity marked as completed.');
    }

    public function markAsCancelled($activityId)
    {
        $activity = Activity::findOrFail($activityId);

        // Check permissions
        $user = Auth::user();
        if (!$user->hasRole('Area Manager') && $activity->user_id !== $user->id) {
            session()->flash('error', 'You can only cancel your own activities.');
            return;
        }

        $activity->markAsCancelled();
        session()->flash('message', 'Activity cancelled.');
    }

    public function render()
    {
        $user = Auth::user();

        $query = Activity::with(['user', 'branch', 'related'])
            ->when(!$user->hasRole('Area Manager'), function ($q) use ($user) {
                return $q->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('branch_id', $user->branch_id);
                });
            })
            ->when($this->search, function ($q) {
                return $q->where(function ($query) {
                    $query->where('subject', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->typeFilter, function ($q) {
                return $q->where('type', $this->typeFilter);
            })
            ->when($this->statusFilter, function ($q) {
                return $q->where('status', $this->statusFilter);
            })
            ->when($this->outcomeFilter, function ($q) {
                return $q->where('outcome', $this->outcomeFilter);
            })
            ->when($this->relatedFilter, function ($q) {
                return $q->where('related_type', $this->relatedFilter);
            })
            ->when($this->dateFilter, function ($q) {
                return match ($this->dateFilter) {
                    'overdue' => $q->overdue(),
                    'today' => $q->dueToday(),
                    'upcoming' => $q->upcoming(7),
                    'this_week' => $q->thisWeek(),
                    'this_month' => $q->thisMonth(),
                    default => $q
                };
            });

        $activities = $query->latest('scheduled_at')
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.activities.activities-list', [
            'activities' => $activities,
        ]);
    }
}
