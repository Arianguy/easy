<?php

namespace App\Livewire\Activities;

use App\Models\Activity;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Customer;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ActivityForm extends Component
{
    public $activity;
    public $type = 'call';
    public $subject = '';
    public $description = '';
    public $status = 'pending';
    public $scheduled_at = '';
    public $duration_minutes = '';
    public $outcome = '';
    public $related_type = '';
    public $related_id = '';
    public $branch_id;

    public $isEditing = false;

    protected function rules()
    {
        return [
            'type' => 'required|in:call,email,meeting,note,task',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled',
            'scheduled_at' => 'nullable|date|after_or_equal:today',
            'duration_minutes' => 'nullable|integer|min:1|max:1440',
            'outcome' => 'nullable|in:successful,unsuccessful,rescheduled,no_response',
            'related_type' => 'nullable|in:App\\Models\\Lead,App\\Models\\Opportunity,App\\Models\\Customer',
            'related_id' => 'nullable|integer',
            'branch_id' => 'required|exists:branches,id',
        ];
    }

    protected function messages()
    {
        return [
            'scheduled_at.after_or_equal' => 'The scheduled date must be today or in the future.',
            'duration_minutes.max' => 'Duration cannot exceed 24 hours (1440 minutes).',
        ];
    }

    public function mount($activity = null)
    {
        $user = Auth::user();

        if ($activity) {
            $this->activity = Activity::findOrFail($activity);
            $this->isEditing = true;

            // Check permissions - Allow editing if user has edit activities permission
            // and either they own the activity OR they can manage all branches (Area Manager)
            if (
                !$user->can('edit activities') ||
                (!$user->canManageAllBranches() && $this->activity->user_id !== $user->id)
            ) {
                abort(403, 'You do not have permission to edit this activity.');
            }

            $this->fill($this->activity->toArray());
            $this->scheduled_at = $this->activity->scheduled_at?->format('Y-m-d\TH:i');
        } else {
            // Set defaults for new activity
            $this->branch_id = $user->canManageAllBranches() ? null : $user->branch_id;

            // If creating from a related model, pre-fill the relationship
            $relatedType = request()->query('related_type');
            $relatedId = request()->query('related_id');

            if ($relatedType && $relatedId) {
                $this->related_type = $relatedType;
                $this->related_id = $relatedId;

                // Set a default subject based on the related model
                $this->setDefaultSubject();
            }
        }
    }

    private function setDefaultSubject()
    {
        if (!$this->related_type || !$this->related_id) return;

        try {
            $model = $this->related_type::find($this->related_id);
            if ($model) {
                $modelName = class_basename($this->related_type);
                $this->subject = "Follow up on {$modelName}: " . ($model->name ?? $model->title ?? 'Untitled');
            }
        } catch (\Exception $e) {
            // Ignore errors in setting default subject
        }
    }

    public function save()
    {
        try {
            $this->validate();

            $user = Auth::user();

            // Check permissions for editing
            if ($this->isEditing && (!$user->can('edit activities') ||
                (!$user->canManageAllBranches() && $this->activity->user_id !== $user->id))) {
                session()->flash('error', 'You do not have permission to edit this activity.');
                return;
            }

            // Check permissions for creating
            if (!$this->isEditing && (!$user->can('create activities') ||
                (!$user->canManageAllBranches() && $this->branch_id !== $user->branch_id))) {
                session()->flash('error', 'You can only create activities in your branch.');
                return;
            }

            $data = [
                'type' => $this->type,
                'subject' => $this->subject,
                'description' => $this->description,
                'status' => $this->status,
                'scheduled_at' => $this->scheduled_at ? \Carbon\Carbon::parse($this->scheduled_at) : null,
                'duration_minutes' => $this->duration_minutes ?: null,
                'outcome' => $this->outcome ?: null,
                'related_type' => $this->related_type ?: null,
                'related_id' => $this->related_id ?: null,
                'branch_id' => $this->branch_id,
            ];

            if ($this->isEditing) {
                $this->activity->update($data);
                session()->flash('message', 'Activity updated successfully.');
            } else {
                $data['user_id'] = $user->id;
                Activity::create($data);
                session()->flash('message', 'Activity created successfully.');
            }

            return redirect()->route('activities.index');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while saving the activity: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('activities.index');
    }

    public function render()
    {
        $user = Auth::user();
        $branches = $user->canManageAllBranches()
            ? Branch::all()
            : Branch::where('id', $user->branch_id)->get();

        // Get related models for dropdown
        $leads = Lead::select('id', 'title', 'customer_id')
            ->with('customer:id,name')
            ->when(!$user->canManageAllBranches(), function ($q) use ($user) {
                return $q->where('branch_id', $user->branch_id);
            })
            ->latest()
            ->take(50)
            ->get();

        $opportunities = Opportunity::select('id', 'name', 'lead_id')
            ->with('lead.customer:id,name')
            ->when(!$user->canManageAllBranches(), function ($q) use ($user) {
                return $q->where('branch_id', $user->branch_id);
            })
            ->latest()
            ->take(50)
            ->get();

        $customers = Customer::select('id', 'name', 'company')
            ->when(!$user->canManageAllBranches(), function ($q) use ($user) {
                return $q->where('branch_id', $user->branch_id);
            })
            ->latest()
            ->take(50)
            ->get();

        return view('livewire.activities.activity-form', [
            'branches' => $branches,
            'leads' => $leads,
            'opportunities' => $opportunities,
            'customers' => $customers,
        ]);
    }
}
