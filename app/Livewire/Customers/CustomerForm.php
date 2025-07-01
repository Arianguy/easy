<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\Branch;
use App\Models\Interest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CustomerForm extends Component
{
    public $customer;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $mobile = '';
    public $company = '';
    public $address = '';
    public $interests = '';
    public $customer_interests = [];
    public $budget_range = '';
    public $source = 'walk_in';
    public $status = 'potential';
    public $notes = '';
    public $branch_id;

    public $isEditing = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'customer_interests' => 'nullable|array',
            'customer_interests.*' => 'exists:interests,id',
            'budget_range' => 'nullable|numeric|min:0',
            'source' => 'required|in:walk_in,referral,online,campaign,cold_call,other',
            'status' => 'required|in:active,inactive,potential',
            'notes' => 'nullable|string',
            'branch_id' => 'required|exists:branches,id',
        ];
    }

    public function mount($customer = null)
    {
        // Initialize customer_interests as empty array
        $this->customer_interests = [];

        if ($customer) {
            $this->customer = Customer::with('interests')->findOrFail($customer);
            $this->isEditing = true;
            $this->fill($this->customer->toArray());

            // Load customer interests
            if (is_string($this->customer->interests)) {
                // Legacy string interests - try to match with existing interests by name
                $interestNames = array_map('trim', explode(',', $this->customer->interests));
                $matchedInterests = Interest::whereIn('name', $interestNames)->pluck('id')->toArray();
                $this->customer_interests = $matchedInterests;
            } elseif ($this->customer->relationLoaded('interests') && $this->customer->interests instanceof \Illuminate\Database\Eloquent\Collection) {
                $this->customer_interests = $this->customer->interests->pluck('id')->toArray();
            } else {
                $this->customer_interests = [];
            }
        } else {
            // Set default branch for new customers
            $user = Auth::user();
            $this->branch_id = $user->hasRole('Area Manager') ? null : $user->branch_id;
        }
    }

    public function save()
    {
        try {
            // Debug: Log the current data
            Log::info('Customer save attempt', [
                'name' => $this->name,
                'branch_id' => $this->branch_id,
                'customer_interests' => $this->customer_interests,
                'isEditing' => $this->isEditing,
            ]);

            $this->validate();

            $user = Auth::user();

            // Check permissions for editing
            if ($this->isEditing && !$user->hasRole('Area Manager') && $this->customer->branch_id !== $user->branch_id) {
                session()->flash('error', 'You can only edit customers in your branch.');
                return;
            }

            // Check permissions for creating
            if (!$this->isEditing && !$user->hasRole('Area Manager') && $this->branch_id !== $user->branch_id) {
                session()->flash('error', 'You can only create customers in your branch.');
                return;
            }

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'mobile' => $this->mobile,
                'company' => $this->company,
                'address' => $this->address,
                'interests' => !empty($this->customer_interests) ? implode(', ', Interest::whereIn('id', $this->customer_interests)->pluck('name')->toArray()) : '',
                'budget_range' => $this->budget_range,
                'source' => $this->source,
                'status' => $this->status,
                'notes' => $this->notes,
                'branch_id' => $this->branch_id,
            ];

            if ($this->isEditing) {
                $this->customer->update($data);

                // Sync interests
                if (!empty($this->customer_interests) && is_array($this->customer_interests)) {
                    $this->customer->interests()->sync($this->customer_interests);
                } else {
                    $this->customer->interests()->sync([]);
                }

                session()->flash('message', 'Customer updated successfully.');
            } else {
                $data['created_by'] = $user->id;
                $customer = Customer::create($data);

                // Sync interests
                if (!empty($this->customer_interests) && is_array($this->customer_interests)) {
                    $customer->interests()->sync($this->customer_interests);
                }

                session()->flash('message', 'Customer created successfully.');
            }

            return redirect()->route('customers.index');
        } catch (\Exception $e) {
            Log::error('Customer save error: ' . $e->getMessage());
            Log::error('Customer save trace: ' . $e->getTraceAsString());
            session()->flash('error', 'An error occurred while saving the customer: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('customers.index');
    }

    public function render()
    {
        $user = Auth::user();
        $branches = $user->hasRole('Area Manager')
            ? Branch::all()
            : Branch::where('id', $user->branch_id)->get();

        return view('livewire.customers.customer-form', [
            'branches' => $branches,
            'allInterests' => Interest::active()->ordered()->get(),
        ]);
    }
}
