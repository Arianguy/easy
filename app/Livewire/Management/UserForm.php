<?php

namespace App\Livewire\Management;

use App\Models\User;
use App\Models\Branch;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserForm extends Component
{
    public $user;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $is_active = true;
    public $branch_id;
    public $selected_roles = [];

    public $isEditing = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($this->isEditing ? $this->user->id : 'NULL'),
            'password' => $this->isEditing ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
            'is_active' => 'required|boolean',
            'branch_id' => 'required|exists:branches,id',
            'selected_roles' => 'required|array|min:1',
            'selected_roles.*' => 'exists:roles,name',
        ];
    }

    public function mount($user = null)
    {
        if ($user) {
            $this->user = User::with('roles')->findOrFail($user);
            $this->isEditing = true;
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->is_active = $this->user->is_active;
            $this->branch_id = $this->user->branch_id;
            $this->selected_roles = $this->user->roles->pluck('name')->toArray();
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'branch_id' => $this->branch_id,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEditing) {
            $this->user->update($data);
            $user = $this->user;
            session()->flash('message', 'User updated successfully.');
        } else {
            $user = User::create($data);
            session()->flash('message', 'User created successfully.');
        }

        // Sync roles
        $user->syncRoles($this->selected_roles);

        return redirect()->route('users.index');
    }

    public function cancel()
    {
        return redirect()->route('users.index');
    }

    public function render()
    {
        $branches = Branch::all();
        $roles = Role::all();

        return view('livewire.management.user-form', [
            'branches' => $branches,
            'roles' => $roles,
        ]);
    }
}
