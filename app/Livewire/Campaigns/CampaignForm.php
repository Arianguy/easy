<?php

namespace App\Livewire\Campaigns;

use App\Models\Campaign;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CampaignForm extends Component
{
    public $campaign;
    public $name = '';
    public $description = '';
    public $type = 'email';
    public $status = 'draft';
    public $start_date = '';
    public $end_date = '';
    public $budget = '';
    public $actual_cost = '';
    public $target_audience = '';
    public $reached_audience = '';
    public $leads_generated = '';
    public $conversions = '';
    public $metrics = '';
    public $branch_id;

    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'type' => 'required|in:email,social_media,print,radio,tv,online,direct_mail,event,other',
        'status' => 'required|in:draft,active,paused,completed,cancelled',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after:start_date',
        'budget' => 'nullable|numeric|min:0',
        'actual_cost' => 'nullable|numeric|min:0',
        'target_audience' => 'nullable|string',
        'reached_audience' => 'nullable|integer|min:0',
        'leads_generated' => 'nullable|integer|min:0',
        'conversions' => 'nullable|integer|min:0',
        'metrics' => 'nullable|string',
        'branch_id' => 'required|exists:branches,id',
    ];

    public function mount($campaign = null)
    {
        if ($campaign) {
            $this->campaign = Campaign::findOrFail($campaign);
            $this->isEditing = true;
            $this->fill($this->campaign->toArray());

            // Format dates for input fields
            $this->start_date = $this->campaign->start_date?->format('Y-m-d');
            $this->end_date = $this->campaign->end_date?->format('Y-m-d');

            // Convert metrics array to string for display
            if (is_array($this->campaign->metrics)) {
                $this->metrics = json_encode($this->campaign->metrics);
            }
        } else {
            // Set default branch for new campaigns
            $user = Auth::user();
            $this->branch_id = $user->hasRole('Area Manager') ? null : $user->branch_id;
        }
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        // Check permissions for editing
        if ($this->isEditing && !$user->hasRole('Area Manager') && $this->campaign->branch_id !== $user->branch_id) {
            session()->flash('error', 'You can only edit campaigns in your branch.');
            return;
        }

        // Check permissions for creating
        if (!$this->isEditing && !$user->hasRole('Area Manager') && $this->branch_id !== $user->branch_id) {
            session()->flash('error', 'You can only create campaigns in your branch.');
            return;
        }

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
            'budget' => $this->budget ?: null,
            'actual_cost' => $this->actual_cost ?: null,
            'target_audience' => $this->target_audience,
            'reached_audience' => $this->reached_audience ?: null,
            'leads_generated' => $this->leads_generated ?: null,
            'conversions' => $this->conversions ?: null,
            'metrics' => $this->metrics ? json_decode($this->metrics, true) : null,
            'branch_id' => $this->branch_id,
        ];

        if ($this->isEditing) {
            $this->campaign->update($data);
            session()->flash('message', 'Campaign updated successfully.');
        } else {
            $data['created_by'] = $user->id;
            Campaign::create($data);
            session()->flash('message', 'Campaign created successfully.');
        }

        return redirect()->route('campaigns.index');
    }

    public function cancel()
    {
        return redirect()->route('campaigns.index');
    }

    public function render()
    {
        $user = Auth::user();
        $branches = $user->hasRole('Area Manager')
            ? Branch::all()
            : Branch::where('id', $user->branch_id)->get();

        return view('livewire.campaigns.campaign-form', [
            'branches' => $branches,
        ]);
    }
}
