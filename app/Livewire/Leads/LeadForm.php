<?php

namespace App\Livewire\Leads;

use App\Models\Activity;
use App\Models\Campaign;
use App\Models\Customer;
use App\Models\Interest;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class LeadForm extends Component
{
    public $leadId;
    public $step = 1; // Multi-step form: 1 = Mobile, 2 = Customer Details, 3 = Lead Details

    // Customer fields
    public $mobile = '';
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $address = '';
    public $company = '';
    public $customer_location = '';
    public $customer_interests = [];
    public $age_group = '';
    public $phone = '';
    public $location = '';
    public $remarks = '';

    // Lead fields
    public $title = '';
    public $description = '';
    public $status = 'new';
    public $priority = 'medium';
    public $source = 'walk_in';
    public $follow_up_date = '';
    public $estimated_value = '';
    public $expected_close_date = '';
    public $campaign_id = '';
    public $rating = '';
    public $assigned_user_id = '';
    public $member_type = 'standard';
    public $notes = '';

    // State management
    public $existingCustomer = null;
    public $showCustomerDetails = false;
    public $mobileExists = false;
    public $mobileCheckMessage = '';
    public $mobileEditEnabled = false;
    public $isConverted = false; // Add property to track if lead is converted

    protected $rules = [
        'mobile' => 'required|regex:/^[0-9]{10}$/',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'title' => 'required|string|max:255',
        'status' => 'required|in:new,contacted,qualified,converted,lost',
        'priority' => 'required|in:low,medium,high',
        'source' => 'required|in:walk_in,referral,online,campaign,cold_call,other',
        'follow_up_date' => 'required|date|after_or_equal:today',
        'estimated_value' => 'nullable|numeric|min:0',
        'expected_close_date' => 'nullable|date|after:today',
        'assigned_user_id' => 'required|exists:users,id',
    ];

    protected $messages = [
        'mobile.required' => 'Mobile number is required.',
        'mobile.regex' => 'Mobile number must be exactly 10 digits.',
        'first_name.required' => 'First name is required.',
        'last_name.required' => 'Last name is required.',
        'title.required' => 'Lead title is required.',
        'status.required' => 'Status is required.',
        'priority.required' => 'Priority is required.',
        'source.required' => 'Source is required.',
        'follow_up_date.required' => 'Follow-up date is required.',
        'follow_up_date.after_or_equal' => 'Follow-up date must be today or in the future.',
        'assigned_user_id.required' => 'Assigned user is required.',
        'assigned_user_id.exists' => 'Selected user does not exist.',
    ];

    public function mount($lead = null)
    {
        // Initialize customer_interests as empty array
        $this->customer_interests = [];

        // Handle both Lead model and leadId parameter
        if ($lead instanceof Lead) {
            $this->leadId = $lead->id;
        } elseif (is_numeric($lead)) {
            $this->leadId = $lead;
        } else {
            $this->leadId = null;
        }

        $this->assigned_user_id = auth()->id() ?? '';

        if ($this->leadId) {
            $this->loadLead();
            // Ensure we're on step 3 for editing
            $this->step = 3;
            // Set flags for edit mode
            $this->mobileExists = true;
            $this->showCustomerDetails = true;
        } else {
            // Start at step 1 for new leads
            $this->step = 1;
        }
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

        // Only auto-check in step 1, but don't auto-advance
        if ($this->step == 1 && strlen($this->mobile) == 10) {
            $this->checkMobileExistsWithMessage();
        }
    }

    public function enableMobileEdit()
    {
        $this->mobileEditEnabled = true;
        $this->mobileCheckMessage = '';
    }

    public function checkMobileOnBlur()
    {
        if (strlen($this->mobile) == 10) {
            $this->checkMobileExistsWithMessage();
        } elseif (strlen($this->mobile) > 0) {
            $this->mobileCheckMessage = 'Mobile number must be exactly 10 digits.';
            $this->mobileExists = false;
        } else {
            $this->mobileCheckMessage = '';
            $this->mobileExists = false;
        }
    }

    public function checkMobileExistsWithMessage()
    {
        try {
            $this->validate(['mobile' => 'required|regex:/^[0-9]{10}$/']);

            $customer = Customer::with('interests')->where('mobile', '+91' . $this->mobile)->first();

            if ($customer) {
                $this->existingCustomer = $customer;
                $this->mobileExists = true;
                $this->showCustomerDetails = true;
                $this->mobileCheckMessage = 'Customer found! Details will be pre-filled.';

                // Pre-fill customer details
                $this->first_name = explode(' ', $customer->name)[0] ?? '';
                $this->last_name = explode(' ', $customer->name, 2)[1] ?? '';
                $this->email = $customer->email ?? '';
                $this->address = $customer->address ?? '';
                $this->company = $customer->company ?? '';
                // Handle both string interests (legacy) and relationship interests
                if (is_string($customer->interests)) {
                    // Legacy string interests - try to match with existing interests by name
                    $interestNames = array_map('trim', explode(',', $customer->interests));
                    $matchedInterests = Interest::whereIn('name', $interestNames)->pluck('id')->toArray();
                    $this->customer_interests = $matchedInterests;
                } elseif ($customer->relationLoaded('interests') && $customer->interests instanceof \Illuminate\Database\Eloquent\Collection) {
                    $this->customer_interests = $customer->interests->pluck('id')->toArray();
                } else {
                    $this->customer_interests = [];
                }
                $this->phone = $customer->phone ?? '';
                $this->remarks = $customer->notes ?? '';
            } else {
                $this->existingCustomer = null;
                $this->mobileExists = false;
                $this->showCustomerDetails = true;
                $this->mobileCheckMessage = 'New mobile number - customer details will be created.';
            }
        } catch (\Exception $e) {
            $this->mobileCheckMessage = 'Please enter a valid 10-digit mobile number.';
            $this->mobileExists = false;
        }
    }

    public function checkMobileExists()
    {
        $this->validate(['mobile' => 'required|regex:/^[0-9]{10}$/']);

        $customer = Customer::with('interests')->where('mobile', '+91' . $this->mobile)->first();

        if ($customer) {
            $this->existingCustomer = $customer;
            $this->mobileExists = true;
            $this->showCustomerDetails = true;
            $this->mobileCheckMessage = 'Customer found! Details will be pre-filled.';

            // Pre-fill customer details
            $this->first_name = explode(' ', $customer->name)[0] ?? '';
            $this->last_name = explode(' ', $customer->name, 2)[1] ?? '';
            $this->email = $customer->email ?? '';
            $this->address = $customer->address ?? '';
            $this->company = $customer->company ?? '';
            // Handle both string interests (legacy) and relationship interests
            if (is_string($customer->interests)) {
                // Legacy string interests - try to match with existing interests by name
                $interestNames = array_map('trim', explode(',', $customer->interests));
                $matchedInterests = Interest::whereIn('name', $interestNames)->pluck('id')->toArray();
                $this->customer_interests = $matchedInterests;
            } elseif ($customer->relationLoaded('interests') && $customer->interests instanceof \Illuminate\Database\Eloquent\Collection) {
                $this->customer_interests = $customer->interests->pluck('id')->toArray();
            } else {
                $this->customer_interests = [];
            }
            $this->phone = $customer->phone ?? '';
            $this->remarks = $customer->notes ?? '';

            // Stay on step 1, don't auto-advance
        } else {
            $this->existingCustomer = null;
            $this->mobileExists = false;
            $this->showCustomerDetails = true;
            $this->mobileCheckMessage = 'New mobile number - customer details will be created.';

            // Stay on step 1, don't auto-advance
        }
    }

    public function nextStep()
    {
        if ($this->step == 1) {
            $this->validate(['mobile' => 'required|regex:/^[0-9]{10}$/']);
            // Check mobile exists but don't auto-advance - user clicked Continue
            $this->checkMobileExists();
            // Now advance to step 2
            $this->step = 2;
        } elseif ($this->step == 2) {
            $this->validateCustomerDetails();
            $this->step = 3;
            // Clear mobile check message when moving to step 3
            $this->mobileCheckMessage = '';
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function validateCustomerDetails()
    {
        // If mobile exists (existing customer), don't validate customer details
        // as they should be read-only
        if (!$this->mobileExists) {
            $this->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
            ]);
        }
    }

    public function isReadOnly()
    {
        return $this->isConverted;
    }

    public function save()
    {
        // Prevent saving if lead is converted
        if ($this->isConverted) {
            session()->flash('error', 'Cannot modify a converted lead.');
            return;
        }

        $this->validate();

        try {
            // Create or update customer
            $customerData = [
                'name' => trim($this->first_name . ' ' . $this->last_name),
                'mobile' => '+91' . $this->mobile,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'company' => $this->company,
                'interests' => !empty($this->customer_interests) ? implode(', ', Interest::whereIn('id', $this->customer_interests)->pluck('name')->toArray()) : '',
                'notes' => $this->remarks,
                'branch_id' => auth()->user()?->branch_id ?? 1,
                'created_by' => auth()->id(),
            ];

            if ($this->existingCustomer) {
                $customer = $this->existingCustomer;
                $customer->update($customerData);
            } else {
                $customer = Customer::create($customerData);
            }

            // Sync interests
            if (!empty($this->customer_interests) && is_array($this->customer_interests)) {
                $customer->interests()->sync($this->customer_interests);
            } else {
                $customer->interests()->sync([]);
            }

            // Create or update lead
            $leadData = [
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
                'priority' => $this->priority,
                'source' => $this->source,
                'follow_up_date' => $this->follow_up_date,
                'estimated_value' => $this->estimated_value ?: null,
                'expected_close_date' => $this->expected_close_date ?: null,
                'notes' => $this->notes,
                'customer_id' => $customer->id,
                'assigned_user_id' => $this->assigned_user_id,
                'branch_id' => auth()->user()?->branch_id ?? 1,
                'created_by' => auth()->id(),
            ];

            if ($this->leadId) {
                $lead = Lead::findOrFail($this->leadId);
                $lead->update($leadData);

                // Create activity for lead update
                Activity::createFor(
                    $lead,
                    'note',
                    'Lead Updated',
                    "Lead '{$lead->title}' was updated by " . auth()->user()->name,
                    auth()->id(),
                    $lead->branch_id
                );

                session()->flash('message', 'Lead updated successfully!');
            } else {
                $lead = Lead::create($leadData);

                // Create activity for lead creation
                Activity::createFor(
                    $lead,
                    'note',
                    'Lead Created',
                    "New lead '{$lead->title}' was created by " . auth()->user()->name,
                    auth()->id(),
                    $lead->branch_id
                );

                session()->flash('message', 'Lead created successfully!');
            }

            return redirect()->route('leads.index');
        } catch (\Exception $e) {
            \Log::error('Lead save error: ' . $e->getMessage());
            \Log::error('Lead save trace: ' . $e->getTraceAsString());
            session()->flash('error', 'An error occurred while saving the lead: ' . $e->getMessage());
        }
    }

    private function loadLead()
    {
        $lead = Lead::with('customer.interests')->findOrFail($this->leadId);

        // Check if lead is converted
        $this->isConverted = $lead->status === 'converted';

        // Load customer data
        if ($lead->customer) {
            // Remove +91 prefix for display in input
            $this->mobile = str_replace('+91', '', $lead->customer->mobile);
            $this->first_name = explode(' ', $lead->customer->name)[0] ?? '';
            $this->last_name = explode(' ', $lead->customer->name, 2)[1] ?? '';
            $this->email = $lead->customer->email ?? '';
            $this->address = $lead->customer->address ?? '';
            $this->company = $lead->customer->company ?? '';
            // Handle both string interests (legacy) and relationship interests
            if (is_string($lead->customer->interests)) {
                // Legacy string interests - try to match with existing interests by name
                $interestNames = array_map('trim', explode(',', $lead->customer->interests));
                $matchedInterests = Interest::whereIn('name', $interestNames)->pluck('id')->toArray();
                $this->customer_interests = $matchedInterests;
            } elseif ($lead->customer->relationLoaded('interests') && $lead->customer->interests instanceof \Illuminate\Database\Eloquent\Collection) {
                $this->customer_interests = $lead->customer->interests->pluck('id')->toArray();
            } else {
                $this->customer_interests = [];
            }
            $this->phone = $lead->customer->phone ?? '';
            $this->remarks = $lead->customer->notes ?? '';
            $this->existingCustomer = $lead->customer;
            $this->mobileExists = true;
            $this->showCustomerDetails = true;
        }

        // Load lead data
        $this->title = $lead->title;
        $this->description = $lead->description;
        $this->status = $lead->status;
        $this->priority = $lead->priority;
        $this->source = $lead->source;
        $this->follow_up_date = $lead->follow_up_date?->format('Y-m-d');
        $this->estimated_value = $lead->estimated_value;
        $this->expected_close_date = $lead->expected_close_date?->format('Y-m-d');
        $this->assigned_user_id = $lead->assigned_user_id;
        $this->notes = $lead->notes;

        // Skip all steps and go directly to lead details for editing
        $this->step = 3;
    }

    public function render()
    {
        return view('livewire.leads.lead-form', [
            'campaigns' => Campaign::where('branch_id', auth()->user()->branch_id)->get(),
            'users' => User::where('branch_id', auth()->user()->branch_id)->get(),
            'interests' => Interest::active()->ordered()->get(),
            'leadSources' => [
                'walk_in' => 'Walk In',
                'referral' => 'Referral',
                'online' => 'Online',
                'campaign' => 'Campaign',
                'cold_call' => 'Cold Call',
                'other' => 'Other'
            ],
            'ageGroups' => [
                '18-25' => '18-25',
                '26-35' => '26-35',
                '36-45' => '36-45',
                '46-55' => '46-55',
                '56-65' => '56-65',
                '65+' => '65+'
            ],
            'ratings' => [
                'hot' => 'Hot',
                'warm' => 'Warm',
                'cold' => 'Cold'
            ]
        ]);
    }
}
