<?php

namespace App\Livewire\Opportunities;

use App\Models\Activity;
use App\Models\Opportunity;
use App\Models\Lead;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OpportunityForm extends Component
{
    public $opportunity;
    public $name = '';
    public $value = '';
    public $stage = 'prospecting';
    public $probability = '';
    public $expected_close_date = '';
    public $actual_close_date = '';
    public $description = '';
    public $close_reason = '';
    public $products_services = '';
    public $lead_id = '';
    public $branch_id;

    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'value' => 'required|numeric|min:0',
        'stage' => 'required|in:prospecting,proposal,negotiation,won,lost',
        'probability' => 'required|numeric|min:0|max:100',
        'expected_close_date' => 'nullable|date',
        'actual_close_date' => 'nullable|date',
        'description' => 'nullable|string',
        'close_reason' => 'nullable|string|max:255',
        'products_services' => 'nullable|string',
        'lead_id' => 'required|exists:leads,id',
        'branch_id' => 'required|exists:branches,id',
    ];

    protected $messages = [
        'name.required' => 'Opportunity name is required.',
        'value.required' => 'Opportunity value is required.',
        'value.numeric' => 'Opportunity value must be a number.',
        'value.min' => 'Opportunity value must be greater than or equal to 0.',
        'stage.required' => 'Stage is required.',
        'probability.required' => 'Probability is required.',
        'probability.numeric' => 'Probability must be a number.',
        'probability.min' => 'Probability must be at least 0%.',
        'probability.max' => 'Probability cannot exceed 100%.',

        'lead_id.required' => 'Associated lead is required.',
        'lead_id.exists' => 'Selected lead does not exist.',
        'branch_id.required' => 'Branch is required.',
        'branch_id.exists' => 'Selected branch does not exist.',
    ];

    public function mount($opportunity = null)
    {
        if ($opportunity) {
            $this->opportunity = Opportunity::findOrFail($opportunity);
            $this->isEditing = true;
            $this->fill([
                'name' => $this->opportunity->name,
                'value' => $this->opportunity->value,
                'stage' => $this->opportunity->stage,
                'probability' => $this->opportunity->probability,
                'expected_close_date' => $this->opportunity->expected_close_date?->format('Y-m-d'),
                'actual_close_date' => $this->opportunity->actual_close_date?->format('Y-m-d'),
                'description' => $this->opportunity->description,
                'close_reason' => $this->opportunity->close_reason,
                'products_services' => is_array($this->opportunity->products_services)
                    ? implode(', ', $this->opportunity->products_services)
                    : $this->opportunity->products_services,
                'lead_id' => $this->opportunity->lead_id,
                'branch_id' => $this->opportunity->branch_id,
            ]);
        } else {
            // Set default branch for new opportunities
            $user = Auth::user();
            $this->branch_id = $user->canManageAllBranches() ? null : $user->branch_id;
            $this->probability = 25; // Default probability for prospecting stage
        }
    }

    public function updatedStage()
    {
        // Auto-update probability based on stage
        $this->probability = match ($this->stage) {
            'prospecting' => 25,
            'proposal' => 50,
            'negotiation' => 75,
            'won' => 100,
            'lost' => 0,
            default => $this->probability
        };

        // Clear/set actual close date based on stage
        if (in_array($this->stage, ['won', 'lost'])) {
            if (!$this->actual_close_date) {
                $this->actual_close_date = now()->format('Y-m-d');
            }
        } else {
            $this->actual_close_date = '';
        }
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        // Check permissions for editing
        if ($this->isEditing && !$user->canManageAllBranches() && $this->opportunity->branch_id !== $user->branch_id) {
            session()->flash('error', 'You can only edit opportunities in your branch.');
            return;
        }

        // Check permissions for creating
        if (!$this->isEditing && !$user->canManageAllBranches() && $this->branch_id !== $user->branch_id) {
            session()->flash('error', 'You can only create opportunities in your branch.');
            return;
        }

        // Validate lead belongs to the same branch
        $lead = Lead::findOrFail($this->lead_id);
        if ($lead->branch_id !== $this->branch_id) {
            session()->flash('error', 'Selected lead must belong to the same branch as the opportunity.');
            return;
        }

        $data = [
            'name' => $this->name,
            'value' => $this->value,
            'stage' => $this->stage,
            'probability' => $this->probability,
            'expected_close_date' => $this->expected_close_date ?: null,
            'actual_close_date' => $this->actual_close_date ?: null,
            'description' => $this->description,
            'close_reason' => $this->close_reason,
            'products_services' => $this->products_services ? explode(', ', $this->products_services) : null,
            'lead_id' => $this->lead_id,
            'branch_id' => $this->branch_id,
        ];

        if ($this->isEditing) {
            $this->opportunity->update($data);

            // Create activity for opportunity update
            Activity::createFor(
                $this->opportunity,
                'note',
                'Opportunity Updated',
                "Opportunity '{$this->opportunity->name}' was updated to stage '{$this->stage}' by " . $user->name,
                $user->id,
                $this->opportunity->branch_id
            );

            session()->flash('message', 'Opportunity updated successfully.');
        } else {
            $data['created_by'] = $user->id;
            $opportunity = Opportunity::create($data);

            // Create activity for opportunity creation
            Activity::createFor(
                $opportunity,
                'note',
                'Opportunity Created',
                "New opportunity '{$opportunity->name}' was created by " . $user->name,
                $user->id,
                $opportunity->branch_id
            );

            session()->flash('message', 'Opportunity created successfully.');
        }

        return redirect()->route('opportunities.index');
    }

    public function cancel()
    {
        return redirect()->route('opportunities.index');
    }

    public function render()
    {
        $user = Auth::user();

        $branches = $user->canManageAllBranches()
            ? Branch::all()
            : Branch::where('id', $user->branch_id)->get();

        $leads = $this->branch_id
            ? Lead::where('branch_id', $this->branch_id)->with('customer')->get()
            : collect();

        return view('livewire.opportunities.opportunity-form', [
            'branches' => $branches,
            'leads' => $leads,
        ]);
    }
}
