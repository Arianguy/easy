<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsList extends Component
{
    use WithPagination;

    public function mount()
    {
        // Check if user has permission to view permissions
        if (!auth()->user()?->can('view permissions')) {
            abort(403, 'Unauthorized to view permissions.');
        }
    }

    public $search = '';
    public $perPage = 10;
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deletePermission($permissionId)
    {
        // Check if user has permission to delete permissions
        if (!auth()->user()?->can('delete permissions')) {
            session()->flash('error', 'Unauthorized to delete permissions.');
            return;
        }

        try {
            $permission = Permission::findOrFail($permissionId);

            // Check if permission is assigned to any roles
            if ($permission->roles()->count() > 0) {
                session()->flash('error', 'Cannot delete permission "' . $permission->name . '" as it is assigned to one or more roles.');
                return;
            }

            $permission->delete();
            session()->flash('success', 'Permission deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting permission: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $allPermissions = Permission::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        // Group permissions by category
        $groupedPermissions = $this->groupPermissionsByCategory($allPermissions);

        return view('livewire.management.permissions-list', [
            'groupedPermissions' => $groupedPermissions,
            'totalPermissions' => $allPermissions->count(),
        ]);
    }

    private function groupPermissionsByCategory($permissions)
    {
        $groups = [];

        foreach ($permissions as $permission) {
            $category = $this->getPermissionCategory($permission->name);

            if (!isset($groups[$category])) {
                $groups[$category] = [
                    'name' => $category,
                    'icon' => $this->getCategoryIcon($category),
                    'color' => $this->getCategoryColor($category),
                    'permissions' => collect()
                ];
            }

            $groups[$category]['permissions']->push($permission);
        }

        // Sort groups by name and sort permissions within each group
        $sortedGroups = collect($groups)->sortKeys()->map(function ($group) {
            $group['permissions'] = $group['permissions']->sortBy('name');
            return $group;
        });

        return $sortedGroups;
    }

    private function getPermissionCategory($permissionName)
    {
        if (str_contains($permissionName, 'customer')) return 'Customers';
        if (str_contains($permissionName, 'lead')) return 'Leads';
        if (str_contains($permissionName, 'opportunity') || str_contains($permissionName, 'opportunities')) return 'Opportunities';
        if (str_contains($permissionName, 'campaign')) return 'Campaigns';
        if (str_contains($permissionName, 'activity') || str_contains($permissionName, 'activities')) return 'Activities';
        if (str_contains($permissionName, 'branch')) return 'Branches';
        if (str_contains($permissionName, 'user')) return 'Users';
        if (str_contains($permissionName, 'permission')) return 'Permissions';
        if (str_contains($permissionName, 'report')) return 'Reports';
        if (str_contains($permissionName, 'system') || str_contains($permissionName, 'manage')) return 'System';

        return 'General';
    }

    private function getCategoryIcon($category)
    {
        $icons = [
            'Customers' => 'users',
            'Leads' => 'user-plus',
            'Opportunities' => 'chart-pie',
            'Campaigns' => 'megaphone',
            'Activities' => 'calendar',
            'Branches' => 'building-office',
            'Users' => 'user-group',
            'Permissions' => 'key',
            'Reports' => 'document-chart-bar',
            'System' => 'cog-6-tooth',
            'General' => 'squares-plus'
        ];

        return $icons[$category] ?? 'squares-plus';
    }

    private function getCategoryColor($category)
    {
        $colors = [
            'Customers' => 'blue',
            'Leads' => 'green',
            'Opportunities' => 'purple',
            'Campaigns' => 'orange',
            'Activities' => 'yellow',
            'Branches' => 'indigo',
            'Users' => 'pink',
            'Permissions' => 'red',
            'Reports' => 'cyan',
            'System' => 'gray',
            'General' => 'slate'
        ];

        return $colors[$category] ?? 'gray';
    }
}
