<?php

namespace App\Livewire\Management;

use App\Models\Branch;
use Livewire\Component;
use Livewire\WithPagination;

class BranchesList extends Component
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

    public function toggleStatus($branchId)
    {
        $branch = Branch::findOrFail($branchId);

        $branch->update([
            'status' => $branch->status === 'active' ? 'inactive' : 'active'
        ]);

        session()->flash('message', 'Branch status updated successfully.');
    }

    public function render()
    {
        $query = Branch::query()
            ->withCount(['users', 'customers'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%')
                        ->orWhere('city', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc');

        $branches = $query->paginate(15);

        return view('livewire.management.branches-list', [
            'branches' => $branches,
        ]);
    }
}
