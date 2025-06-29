<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
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
    public $budget_range = '';
    public $source = 'walk_in';
    public $status = 'potential';
    public $notes = '';
    public $branch_id;

    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'mobile' => 'nullable|string|max:20',
        'company' => 'nullable|string|max:255',
        'address' => 'nullable|string',
        'interests' => 'nullable|string',
        'budget_range' => 'nullable|numeric|min:0',
        'source' => 'required|in:walk_in,referral,online,campaign,cold_call,other',
        'status' => 'required|in:active,inactive,potential',
        'notes' => 'nullable|string',
        'branch_id' => 'required|exists:branches,id',
    ];

    public function mount($customer = null)
    {
        if ($customer) {
            $this->customer = Customer::findOrFail($customer);
            $this->isEditing = true;
            $this->fill($this->customer->toArray());
        } else {
            // Set default branch for new customers
            $user = Auth::user();
            $this->branch_id = $user->hasRole('Area Manager') ? null : $user->branch_id;
        }
    }

    public function save()
    {
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
            'interests' => $this->interests,
            'budget_range' => $this->budget_range,
            'source' => $this->source,
            'status' => $this->status,
            'notes' => $this->notes,
            'branch_id' => $this->branch_id,
        ];

        if ($this->isEditing) {
            $this->customer->update($data);
            session()->flash('message', 'Customer updated successfully.');
        } else {
            $data['created_by'] = $user->id;
            Customer::create($data);
            session()->flash('message', 'Customer created successfully.');
        }

        return redirect()->route('customers.index');
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
        ]);
    }
}
