<?php

namespace App\Livewire\Opportunities;

use App\Models\Opportunity;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OpportunitiesList extends Component
{
    use WithPagination;

    public $search = '';
    public $stageFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'stageFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStageFilter()
    {
        $this->resetPage();
    }

    public function markAsWon($opportunityId)
    {
        $opportunity = Opportunity::findOrFail($opportunityId);

        // Check if user can modify this opportunity
        $user = Auth::user();
        if (!$user->canManageAllBranches() && $opportunity->branch_id !== $user->branch_id) {
            session()->flash('error', 'You can only modify opportunities in your branch.');
            return;
        }

        $opportunity->markAsWon();
        session()->flash('message', 'Opportunity marked as won!');
    }

    public function markAsLost($opportunityId)
    {
        $opportunity = Opportunity::findOrFail($opportunityId);

        // Check if user can modify this opportunity
        $user = Auth::user();
        if (!$user->canManageAllBranches() && $opportunity->branch_id !== $user->branch_id) {
            session()->flash('error', 'You can only modify opportunities in your branch.');
            return;
        }

        $opportunity->markAsLost();
        session()->flash('message', 'Opportunity marked as lost.');
    }

    public function render()
    {
        $query = Opportunity::query()
            ->with(['lead.customer', 'branch', 'creator'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('lead.customer', function ($customerQuery) {
                            $customerQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('company', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->stageFilter, function ($query) {
                $query->where('stage', $this->stageFilter);
            })
            ->orderBy('created_at', 'desc');

        // Apply branch scope for non-Area Managers
        $user = Auth::user();
        if (!$user->canManageAllBranches()) {
            $query->where('branch_id', $user->branch_id);
        }

        $opportunities = $query->paginate(15);

        return view('livewire.opportunities.opportunities-list', [
            'opportunities' => $opportunities,
        ]);
    }
}
