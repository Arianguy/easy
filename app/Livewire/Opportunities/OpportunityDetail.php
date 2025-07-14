<?php

namespace App\Livewire\Opportunities;

use App\Models\Activity;
use App\Models\Opportunity;
use App\Models\Lead;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OpportunityDetail extends Component
{
    public $opportunity;
    public $activeTab = 'overview';

    // Quick action properties
    public $showTaskModal = false;
    public $showNoteModal = false;
    public $showActivityModal = false;

    // Task form properties
    public $taskSubject = '';
    public $taskDescription = '';
    public $taskScheduledAt = '';
    public $taskType = 'task';

    // Note form properties
    public $noteSubject = '';
    public $noteDescription = '';

    // Activity form properties
    public $activityType = 'call';
    public $activitySubject = '';
    public $activityDescription = '';
    public $activityScheduledAt = '';

    // Opportunity edit properties
    public $editMode = true;
    public $name = '';
    public $value = '';
    public $stage = '';
    public $probability = '';
    public $expected_close_date = '';
    public $actual_close_date = '';
    public $description = '';
    public $close_reason = '';
    public $products_services = '';

    protected $rules = [
        'taskSubject' => 'required|string|max:255',
        'taskDescription' => 'nullable|string',
        'taskScheduledAt' => 'required|date|after:now',

        'noteSubject' => 'required|string|max:255',
        'noteDescription' => 'nullable|string',

        'activitySubject' => 'required|string|max:255',
        'activityDescription' => 'nullable|string',
        'activityScheduledAt' => 'required|date',

        'name' => 'required|string|max:255',
        'value' => 'required|numeric|min:0',
        'stage' => 'required|in:prospecting,proposal,negotiation,won,lost',
        'probability' => 'required|numeric|min:0|max:100',
        'expected_close_date' => 'nullable|date',
        'actual_close_date' => 'nullable|date',
        'description' => 'nullable|string',
        'close_reason' => 'nullable|string|max:255',
        'products_services' => 'nullable|string',
    ];

    public function mount($opportunity)
    {
        $this->opportunity = Opportunity::with(['lead.customer', 'creator', 'branch'])->findOrFail($opportunity);
        $this->loadOpportunityData();
    }

    public function loadOpportunityData()
    {
        $this->name = $this->opportunity->name;
        $this->value = $this->opportunity->value;
        $this->stage = $this->opportunity->stage;
        $this->probability = $this->opportunity->probability;
        $this->expected_close_date = $this->opportunity->expected_close_date?->format('Y-m-d');
        $this->actual_close_date = $this->opportunity->actual_close_date?->format('Y-m-d');
        $this->description = $this->opportunity->description;
        $this->close_reason = $this->opportunity->close_reason;
        $this->products_services = is_array($this->opportunity->products_services)
            ? implode(', ', $this->opportunity->products_services)
            : $this->opportunity->products_services;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
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

        // Clear close reason if stage is not "lost"
        if ($this->stage !== 'lost') {
            $this->close_reason = '';
        }
    }



    public function saveOpportunity()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'stage' => 'required|in:prospecting,proposal,negotiation,won,lost',
            'probability' => 'required|numeric|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'actual_close_date' => 'nullable|date',
            'description' => 'nullable|string',
            'products_services' => 'nullable|string',
        ];

        // Make close_reason required when stage is "lost"
        if ($this->stage === 'lost') {
            $rules['close_reason'] = 'required|string|max:500';
        } else {
            $rules['close_reason'] = 'nullable|string|max:500';
        }

        $this->validate($rules);

        // Track changes before update
        $originalData = $this->opportunity->toArray();
        $changes = [];

        $newData = [
            'name' => $this->name,
            'value' => $this->value,
            'stage' => $this->stage,
            'probability' => $this->probability,
            'expected_close_date' => $this->expected_close_date ?: null,
            'actual_close_date' => $this->actual_close_date ?: null,
            'description' => $this->description,
            'close_reason' => $this->close_reason,
            'products_services' => $this->products_services ? explode(', ', $this->products_services) : null,
        ];

        // Compare and track changes
        foreach ($newData as $field => $newValue) {
            $oldValue = $originalData[$field] ?? null;

            // Handle arrays (products_services)
            if (is_array($newValue) && is_array($oldValue)) {
                if ($newValue !== $oldValue) {
                    $changes[] = ucfirst(str_replace('_', ' ', $field)) . ': ' .
                        (empty($oldValue) ? 'None' : implode(', ', $oldValue)) . ' → ' .
                        (empty($newValue) ? 'None' : implode(', ', $newValue));
                }
            }
            // Handle dates
            elseif (in_array($field, ['expected_close_date', 'actual_close_date'])) {
                $oldDate = $oldValue ? \Carbon\Carbon::parse($oldValue)->format('M d, Y') : 'None';
                $newDate = $newValue ? \Carbon\Carbon::parse($newValue)->format('M d, Y') : 'None';
                if ($oldDate !== $newDate) {
                    $changes[] = ucfirst(str_replace('_', ' ', $field)) . ': ' . $oldDate . ' → ' . $newDate;
                }
            }
            // Handle other fields
            else {
                if ($oldValue != $newValue) {
                    $displayOld = $oldValue ?: 'None';
                    $displayNew = $newValue ?: 'None';

                    // Format currency for value field
                    if ($field === 'value') {
                        $displayOld = $oldValue ? '₹' . number_format($oldValue, 2) : 'None';
                        $displayNew = $newValue ? '₹' . number_format($newValue, 2) : 'None';
                    }
                    // Format percentage for probability
                    elseif ($field === 'probability') {
                        $displayOld = $oldValue ? $oldValue . '%' : 'None';
                        $displayNew = $newValue ? $newValue . '%' : 'None';
                    }

                    $changes[] = ucfirst(str_replace('_', ' ', $field)) . ': ' . $displayOld . ' → ' . $displayNew;
                }
            }
        }

        $this->opportunity->update($newData);

        // Create activity with detailed changes
        if (!empty($changes)) {
            $changeDescription = "Updated the following:\n" . implode("\n", $changes);

            Activity::createFor(
                $this->opportunity,
                'note',
                'Opportunity Updated',
                $changeDescription,
                Auth::user()->id,
                $this->opportunity->branch_id
            );
        }

        $this->opportunity->refresh();

        session()->flash('message', 'Opportunity updated successfully.');
    }

    public function openTaskModal()
    {
        $this->resetTaskForm();
        $this->showTaskModal = true;
    }

    public function openNoteModal()
    {
        $this->resetNoteForm();
        $this->showNoteModal = true;
    }

    public function openActivityModal()
    {
        $this->resetActivityForm();
        $this->showActivityModal = true;
    }

    public function closeTaskModal()
    {
        $this->showTaskModal = false;
        $this->resetTaskForm();
    }

    public function closeNoteModal()
    {
        $this->showNoteModal = false;
        $this->resetNoteForm();
    }

    public function closeActivityModal()
    {
        $this->showActivityModal = false;
        $this->resetActivityForm();
    }

    public function saveTask()
    {
        $this->validate([
            'taskSubject' => 'required|string|max:255',
            'taskDescription' => 'nullable|string',
            'taskScheduledAt' => 'required|date|after:now',
        ]);

        Activity::create([
            'type' => $this->taskType,
            'subject' => $this->taskSubject,
            'description' => $this->taskDescription,
            'status' => 'pending',
            'scheduled_at' => $this->taskScheduledAt,
            'related_type' => Opportunity::class,
            'related_id' => $this->opportunity->id,
            'user_id' => Auth::user()->id,
            'branch_id' => $this->opportunity->branch_id,
        ]);

        $this->closeTaskModal();
        session()->flash('message', 'Task created successfully.');
    }

    public function saveNote()
    {
        $this->validate([
            'noteSubject' => 'required|string|max:255',
            'noteDescription' => 'nullable|string',
        ]);

        Activity::createFor(
            $this->opportunity,
            'note',
            $this->noteSubject,
            $this->noteDescription,
            Auth::user()->id,
            $this->opportunity->branch_id
        );

        $this->closeNoteModal();
        session()->flash('message', 'Note added successfully.');
    }

    public function saveActivity()
    {
        $this->validate([
            'activitySubject' => 'required|string|max:255',
            'activityDescription' => 'nullable|string',
            'activityScheduledAt' => 'required|date',
        ]);

        Activity::create([
            'type' => $this->activityType,
            'subject' => $this->activitySubject,
            'description' => $this->activityDescription,
            'status' => 'pending',
            'scheduled_at' => $this->activityScheduledAt,
            'related_type' => Opportunity::class,
            'related_id' => $this->opportunity->id,
            'user_id' => Auth::user()->id,
            'branch_id' => $this->opportunity->branch_id,
        ]);

        $this->closeActivityModal();
        session()->flash('message', 'Activity scheduled successfully.');
    }

    private function resetTaskForm()
    {
        $this->taskSubject = '';
        $this->taskDescription = '';
        $this->taskScheduledAt = '';
        $this->taskType = 'task';
    }

    private function resetNoteForm()
    {
        $this->noteSubject = '';
        $this->noteDescription = '';
    }

    private function resetActivityForm()
    {
        $this->activityType = 'call';
        $this->activitySubject = '';
        $this->activityDescription = '';
        $this->activityScheduledAt = '';
    }

    public function render()
    {
        $activities = $this->opportunity->activities()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.opportunities.opportunity-detail', [
            'activities' => $activities,
        ]);
    }
}
