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

    // Mobile validation state
    public $existingCustomer = null;
    public $mobileExists = false;
    public $mobileCheckMessage = '';

    public $isEditing = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'required|regex:/^[0-9]{10}$/',
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

    protected function messages()
    {
        return [
            'mobile.required' => 'Mobile number is required.',
            'mobile.regex' => 'Mobile number must be exactly 10 digits.',
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

            // Handle mobile number - remove +91 prefix for editing
            if ($this->customer->mobile && str_starts_with($this->customer->mobile, '+91')) {
                $this->mobile = substr($this->customer->mobile, 3);
            } else {
                $this->mobile = $this->customer->mobile ?? '';
            }

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
                'mobile' => '+91' . $this->mobile,
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
                // Check if we found an existing customer during mobile check
                if ($this->mobileExists && $this->existingCustomer) {
                    // Update existing customer instead of creating new one
                    $this->existingCustomer->update($data);

                    // Sync interests
                    if (!empty($this->customer_interests) && is_array($this->customer_interests)) {
                        $this->existingCustomer->interests()->sync($this->customer_interests);
                    } else {
                        $this->existingCustomer->interests()->sync([]);
                    }

                    session()->flash('message', 'Existing customer updated successfully.');
                } else {
                    // Create new customer
                    $data['created_by'] = $user->id;
                    $customer = Customer::create($data);

                    // Sync interests
                    if (!empty($this->customer_interests) && is_array($this->customer_interests)) {
                        $customer->interests()->sync($this->customer_interests);
                    }

                    session()->flash('message', 'Customer created successfully.');
                }
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

    public function updatedMobile()
    {
        // Remove any non-numeric characters
        $this->mobile = preg_replace('/[^0-9]/', '', $this->mobile);

        // Limit to 10 digits
        if (strlen($this->mobile) > 10) {
            $this->mobile = substr($this->mobile, 0, 10);
        }

        // Clear previous message when typing
        $this->mobileCheckMessage = '';

        // Auto-check when 10 digits are entered
        if (strlen($this->mobile) == 10) {
            $this->checkMobileExists();
        }
    }

    public function checkMobileExists()
    {
        try {
            $this->validate(['mobile' => 'required|regex:/^[0-9]{10}$/']);

            // Skip check if editing the same customer
            if ($this->isEditing && $this->customer && $this->customer->mobile === '+91' . $this->mobile) {
                $this->mobileCheckMessage = '';
                return;
            }

            $customer = Customer::with('interests')->where('mobile', '+91' . $this->mobile)->first();

            if ($customer) {
                $this->existingCustomer = $customer;
                $this->mobileExists = true;
                $this->mobileCheckMessage = 'Customer with this mobile number already exists! Details have been pre-filled.';

                // Pre-fill customer details
                $this->name = $customer->name;
                $this->email = $customer->email ?? '';
                $this->phone = $customer->phone ?? '';
                $this->company = $customer->company ?? '';
                $this->address = $customer->address ?? '';
                $this->budget_range = $customer->budget_range ?? '';
                $this->source = $customer->source ?? 'walk_in';
                $this->status = $customer->status ?? 'potential';
                $this->notes = $customer->notes ?? '';
                $this->branch_id = $customer->branch_id;

                // Handle interests
                if (is_string($customer->interests)) {
                    // Legacy string interests
                    $interestNames = array_map('trim', explode(',', $customer->interests));
                    $matchedInterests = Interest::whereIn('name', $interestNames)->pluck('id')->toArray();
                    $this->customer_interests = $matchedInterests;
                } elseif ($customer->relationLoaded('interests') && $customer->interests instanceof \Illuminate\Database\Eloquent\Collection) {
                    $this->customer_interests = $customer->interests->pluck('id')->toArray();
                } else {
                    $this->customer_interests = [];
                }
            } else {
                $this->existingCustomer = null;
                $this->mobileExists = false;
                $this->mobileCheckMessage = 'New mobile number - customer will be created with +91 prefix.';
            }
        } catch (\Exception $e) {
            $this->mobileCheckMessage = 'Please enter a valid 10-digit mobile number.';
            $this->mobileExists = false;
        }
    }
}
