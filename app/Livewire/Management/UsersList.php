<?php

namespace App\Livewire\Management;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class UsersList extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function toggleStatus($userId)
    {
        $user = User::findOrFail($userId);

        // Prevent deactivating self
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot deactivate your own account.');
            return;
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        session()->flash('message', 'User status updated successfully.');
    }

    public function getRoleStatsProperty()
    {
        return [
            'Area Manager' => User::role('Area Manager')->count(),
            'Sales Manager' => User::role('Sales Manager')->count(),
            'Sales Executive' => User::role('Sales Executive')->count(),
        ];
    }

    public function render()
    {
        $query = User::query()
            ->with(['roles', 'branch'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->role($this->roleFilter);
            })
            ->orderBy('created_at', 'desc');

        $users = $query->paginate(15);

        return view('livewire.management.users-list', [
            'users' => $users,
        ]);
    }
}
