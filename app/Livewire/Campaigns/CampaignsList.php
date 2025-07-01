<?php

namespace App\Livewire\Campaigns;

use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CampaignsList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function toggleStatus($campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);

        // Check if user can modify this campaign
        $user = Auth::user();
        if (!$user->hasRole('Area Manager') && $campaign->branch_id !== $user->branch_id) {
            session()->flash('error', 'You can only modify campaigns in your branch.');
            return;
        }

        // Toggle between active and paused (don't allow direct activation of completed/cancelled)
        if ($campaign->status === 'active') {
            $campaign->update(['status' => 'paused']);
            session()->flash('message', 'Campaign paused successfully.');
        } elseif ($campaign->status === 'paused') {
            $campaign->update(['status' => 'active']);
            session()->flash('message', 'Campaign activated successfully.');
        } else {
            session()->flash('error', 'Cannot toggle status for this campaign.');
        }
    }

    public function render()
    {
        $query = Campaign::query()
            ->with(['branch', 'creator'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('target_audience', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->orderBy('created_at', 'desc');

        // Apply branch scope for non-Area Managers
        $user = Auth::user();
        if (!$user->hasRole('Area Manager')) {
            $query->where('branch_id', $user->branch_id);
        }

        $campaigns = $query->paginate(15);

        return view('livewire.campaigns.campaigns-list', [
            'campaigns' => $campaigns,
        ]);
    }
}
