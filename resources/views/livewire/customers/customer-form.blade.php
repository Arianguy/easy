<div>
    <flux:header>
        <flux:heading size="lg">{{ $isEditing ? 'Edit Customer' : 'Add Customer' }}</flux:heading>

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
                    <div class="md:col-span-2">
                        <flux:input
                            wire:model="name"
                            label="Full Name"
                            placeholder="Enter customer full name"
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
                        />
                        @error('email')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="phone"
                            label="Phone"
                            placeholder="Enter phone number"
                        />
                        @error('phone')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="mobile"
                            label="Mobile"
                            placeholder="Enter mobile number"
                        />
                        @error('mobile')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="company"
                            label="Company"
                            placeholder="Enter company name"
                        />
                        @error('company')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address & Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Address & Details</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <flux:textarea
                            wire:model="address"
                            label="Address"
                            placeholder="Enter full address"
                            rows="3"
                        />
                        @error('address')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:textarea
                            wire:model="interests"
                            label="Interests"
                            placeholder="Enter customer interests or requirements"
                            rows="3"
                        />
                        @error('interests')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="budget_range"
                            label="Budget Range"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="Enter budget range"
                        />
                        @error('budget_range')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Settings & Assignment -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Settings & Assignment</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Source</label>
                        <select
                            wire:model="source"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="walk_in">Walk In</option>
                            <option value="referral">Referral</option>
                            <option value="online">Online</option>
                            <option value="campaign">Campaign</option>
                            <option value="cold_call">Cold Call</option>
                            <option value="other">Other</option>
                        </select>
                        @error('source')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select
                            wire:model="status"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="potential">Potential</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
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
                        <flux:textarea
                            wire:model="notes"
                            label="Notes"
                            placeholder="Enter any additional notes about this customer"
                            rows="4"
                        />
                        @error('notes')
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
                    {{ $isEditing ? 'Update Customer' : 'Create Customer' }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
