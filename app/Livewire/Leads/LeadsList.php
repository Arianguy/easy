<?php

namespace App\Livewire\Leads;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class LeadsList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $sourceFilter = '';
    public $assignedUserFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'sourceFilter' => ['except' => ''],
        'assignedUserFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function updatingSourceFilter()
    {
        $this->resetPage();
    }

    public function updatingAssignedUserFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->priorityFilter = '';
        $this->sourceFilter = '';
        $this->assignedUserFilter = '';
        $this->resetPage();
    }

    public function deleteLead($leadId)
    {
        // Check permissions first
        if (!Auth::user()->can('delete leads')) {
            session()->flash('error', 'You do not have permission to delete leads.');
            return;
        }

        $lead = Lead::findOrFail($leadId);

        // Check if user can delete this lead
        if (Auth::user()?->canManageAllBranches() || $lead->branch_id === Auth::user()?->branch_id) {
            $lead->delete();
            session()->flash('message', 'Lead deleted successfully!');
        } else {
            session()->flash('error', 'You do not have permission to delete this lead.');
        }
    }

    public function convertToOpportunity($leadId)
    {
        // Check permissions first
        if (!Auth::user()->can('edit leads')) {
            session()->flash('error', 'You do not have permission to convert leads.');
            return;
        }

        $lead = Lead::findOrFail($leadId);

        if ($lead->status !== 'converted') {
            $opportunity = $lead->convertToOpportunity([
                'name' => $lead->title,
                'value' => $lead->estimated_value ?? 0,
                'expected_close_date' => $lead->expected_close_date,
                'description' => $lead->description,
            ]);

            session()->flash('message', 'Lead converted to opportunity successfully!');
        }
    }

    public function render()
    {
        // Temporarily disable all filtering to debug
        $query = Lead::withoutGlobalScope(\App\Scopes\BranchScope::class)
            ->with(['customer', 'assignedUser', 'branch']);

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($customerQuery) {
                        $customerQuery->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('mobile', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Apply filters
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        if ($this->sourceFilter) {
            $query->where('source', $this->sourceFilter);
        }

        if ($this->assignedUserFilter) {
            $query->where('assigned_user_id', $this->assignedUserFilter);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $leads = $query->paginate($this->perPage);

        // Get all users for now
        $users = User::all();

        return view('livewire.leads.leads-list', [
            'leads' => $leads,
            'users' => $users,
            'statuses' => [
                'new' => 'New',
                'contacted' => 'Contacted',
                'qualified' => 'Qualified',
                'converted' => 'Converted',
                'lost' => 'Lost'
            ],
            'priorities' => [
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High'
            ],
            'sources' => [
                'website' => 'Website',
                'social_media' => 'Social Media',
                'referral' => 'Referral',
                'cold_call' => 'Cold Call',
                'email' => 'Email',
                'advertisement' => 'Advertisement',
                'trade_show' => 'Trade Show',
                'other' => 'Other'
            ]
        ]);
    }
}
