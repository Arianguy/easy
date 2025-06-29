<div>
    <flux:header>
        <flux:heading size="lg">{{ $isEditing ? 'Edit User' : 'Add User' }}</flux:heading>

        <flux:spacer />

        <flux:button wire:click="cancel" variant="ghost">
            Cancel
        </flux:button>
    </flux:header>

    <div class="max-w-4xl">
        <form wire:submit="save" class="space-y-8">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Basic Information</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:input
                            wire:model="name"
                            label="Full Name"
                            placeholder="Enter full name"
                            required
                        />
                        @error('name')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="email"
                            label="Email"
                            type="email"
                            placeholder="Enter email address"
                            required
                        />
                        @error('email')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="password"
                            label="{{ $isEditing ? 'New Password (leave blank to keep current)' : 'Password' }}"
                            type="password"
                            placeholder="Enter password"
                            :required="!$isEditing"
                        />
                        @error('password')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="password_confirmation"
                            label="Confirm Password"
                            type="password"
                            placeholder="Confirm password"
                            :required="!$isEditing || !empty($password)"
                        />
                        @error('password_confirmation')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Role & Access -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Role & Access</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select
                            wire:model="is_active"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        @error('is_active')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Branch</label>
                        <select
                            wire:model="branch_id"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Roles</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($roles as $role)
                                <label class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        wire:model="selected_roles"
                                        value="{{ $role->name }}"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800"
                                    >
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $role->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            @if($role->name === 'Area Manager')
                                                Full system access
                                            @elseif($role->name === 'Sales Manager')
                                                Branch management
                                            @elseif($role->name === 'Sales Executive')
                                                Customer & lead management
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('selected_roles')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-4">
                <flux:button type="button" wire:click="cancel" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $isEditing ? 'Update User' : 'Create User' }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
