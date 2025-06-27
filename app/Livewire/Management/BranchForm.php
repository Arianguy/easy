<?php

namespace App\Livewire\Management;

use App\Models\Branch;
use Livewire\Component;

class BranchForm extends Component
{
    public $branch;
    public $name = '';
    public $code = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $postal_code = '';
    public $country = '';
    public $phone = '';
    public $email = '';
    public $status = 'active';
    public $description = '';

    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'nullable|string|max:10|unique:branches,code',
        'address' => 'nullable|string|max:500',
        'city' => 'required|string|max:100',
        'state' => 'nullable|string|max:100',
        'postal_code' => 'nullable|string|max:20',
        'country' => 'nullable|string|max:100',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'status' => 'required|in:active,inactive',
        'description' => 'nullable|string|max:1000',
    ];

    public function mount($branch = null)
    {
        if ($branch) {
            $this->branch = Branch::findOrFail($branch);
            $this->isEditing = true;
            $this->fill($this->branch->toArray());
        }
    }

    public function save()
    {
        // Update validation rules for editing
        if ($this->isEditing) {
            $this->rules['code'] = 'nullable|string|max:10|unique:branches,code,' . $this->branch->id;
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => $this->status,
            'description' => $this->description,
        ];

        if ($this->isEditing) {
            $this->branch->update($data);
            session()->flash('message', 'Branch updated successfully.');
        } else {
            Branch::create($data);
            session()->flash('message', 'Branch created successfully.');
        }

        return redirect()->route('branches.index');
    }

    public function cancel()
    {
        return redirect()->route('branches.index');
    }

    public function render()
    {
        return view('livewire.management.branch-form');
    }
}
