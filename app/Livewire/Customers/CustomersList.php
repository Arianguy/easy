<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CustomersList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function toggleStatus($customerId)
    {
        // Check permissions first
        if (!Auth::user()->can('edit customers')) {
            session()->flash('error', 'You do not have permission to edit customers.');
            return;
        }

        $customer = Customer::findOrFail($customerId);

        // Check if user can modify this customer
        $user = Auth::user();
        if (!$user->hasRole('Area Manager') && $customer->branch_id !== $user->branch_id) {
            session()->flash('error', 'You can only modify customers in your branch.');
            return;
        }

        $customer->update([
            'status' => $customer->status === 'active' ? 'inactive' : 'active'
        ]);

        session()->flash('message', 'Customer status updated successfully.');
    }

    public function render()
    {
        $query = Customer::query()
            ->with(['branch', 'interests'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('mobile', 'like', '%' . $this->search . '%')
                        ->orWhere('company', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc');

        // Apply branch scope for non-Area Managers and non-Super Admins
        $user = Auth::user();
        if (!$user->hasRole('Area Manager') && !$user->hasRole('Super Admin')) {
            $query->where('branch_id', $user->branch_id);
        }

        $customers = $query->paginate(15);

        return view('livewire.customers.customers-list', [
            'customers' => $customers,
        ]);
    }
}
