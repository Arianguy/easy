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
    public $phone = '';
    public $designation = '';
    public $is_active = true;
    public $branch_id;
    public $selected_roles = [];

    public $isEditing = false;

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($this->isEditing ? $this->user->id : 'NULL'),
            'password' => $this->isEditing ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'designation' => 'required|string|max:100',
            'is_active' => 'required|boolean',
            'selected_roles' => 'required|array|min:1',
            'selected_roles.*' => 'exists:roles,name',
        ];

        // Make branch_id required only if not Super Admin
        if (in_array('Super Admin', $this->selected_roles)) {
            $rules['branch_id'] = 'nullable|exists:branches,id';
        } else {
            $rules['branch_id'] = 'required|exists:branches,id';
        }

        return $rules;
    }

    public function mount($user = null)
    {
        if ($user) {
            $this->user = User::with('roles')->findOrFail($user);
            $this->isEditing = true;
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->phone = $this->user->phone ?? '';
            $this->designation = $this->user->designation ?? '';
            $this->is_active = $this->user->is_active;
            $this->branch_id = $this->user->branch_id;
            $this->selected_roles = $this->user->roles->pluck('name')->toArray();
        }
    }

    public function save()
    {
        try {
            $this->validate();

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'designation' => $this->designation,
                'is_active' => $this->is_active,
                'branch_id' => $this->branch_id ?: null, // Convert empty string to null
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions so they display properly
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving user: ' . $e->getMessage());
            return;
        }
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
