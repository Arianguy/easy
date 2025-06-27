<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomerForm extends Component
{
    public $customer;
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $company = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $postal_code = '';
    public $country = '';
    public $status = 'active';
    public $notes = '';
    public $branch_id;

    public $isEditing = false;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'company' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:500',
        'city' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:100',
        'postal_code' => 'nullable|string|max:20',
        'country' => 'nullable|string|max:100',
        'status' => 'required|in:active,inactive',
        'notes' => 'nullable|string|max:1000',
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

        // Check permissions
        if (!$user->hasRole('Area Manager') && $this->branch_id !== $user->branch_id) {
            session()->flash('error', 'You can only create customers in your branch.');
            return;
        }

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'status' => $this->status,
            'notes' => $this->notes,
            'branch_id' => $this->branch_id,
        ];

        if ($this->isEditing) {
            $this->customer->update($data);
            session()->flash('message', 'Customer updated successfully.');
        } else {
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
        ])->layout('components.layouts.app.sidebar', [
            'title' => $this->isEditing ? 'Edit Customer' : 'Add Customer'
        ]);
    }
}
