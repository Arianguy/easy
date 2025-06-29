<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionForm extends Component
{
    public Permission $permission;
    public $name = '';
    public $selectedRoles = [];
    public $isEditing = false;

    protected $messages = [
        'name.required' => 'Permission name is required.',
        'name.unique' => 'This permission name already exists.',
    ];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255|unique:permissions,name',
        ];

        // If editing, exclude current permission from unique validation
        if ($this->isEditing && $this->permission->exists) {
            $rules['name'] = 'required|string|max:255|unique:permissions,name,' . $this->permission->id;
        }

        return $rules;
    }

    public function mount(?Permission $permission = null)
    {
        // Check permissions based on action
        if ($permission && $permission->exists) {
            if (!auth()->user()?->can('edit permissions')) {
                abort(403, 'Unauthorized to edit permissions.');
            }
            $this->permission = $permission;
            $this->name = $permission->name;
            $this->selectedRoles = $permission->roles->pluck('id')->toArray();
            $this->isEditing = true;
        } else {
            if (!auth()->user()?->can('create permissions')) {
                abort(403, 'Unauthorized to create permissions.');
            }
            $this->permission = new Permission();
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEditing) {
                $this->permission->update([
                    'name' => $this->name,
                ]);
                $message = 'Permission updated successfully.';
            } else {
                $this->permission = Permission::create([
                    'name' => $this->name,
                ]);
                $message = 'Permission created successfully.';
            }

            // Sync roles
            if (!empty($this->selectedRoles)) {
                // Convert role IDs to role instances
                $roles = Role::whereIn('id', $this->selectedRoles)->get();
                $this->permission->syncRoles($roles);
            } else {
                $this->permission->syncRoles([]);
            }

            session()->flash('success', $message);
            return redirect()->route('permissions.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving permission: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('permissions.index');
    }

    public function render()
    {
        $roles = Role::orderBy('name')->get();

        return view('livewire.management.permission-form', [
            'roles' => $roles,
        ]);
    }
}
