<flux:main>
    <flux:header>
        <flux:heading size="lg">{{ $isEditing ? 'Edit Branch' : 'Add Branch' }}</flux:heading>

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
                            label="Branch Name"
                            placeholder="Enter branch name"
                            required
                        />
                        @error('name')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="code"
                            label="Branch Code"
                            placeholder="Enter branch code (optional)"
                        />
                        @error('code')
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
                            wire:model="email"
                            label="Email"
                            type="email"
                            placeholder="Enter email address"
                        />
                        @error('email')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Address Information</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <flux:input
                            wire:model="address"
                            label="Address"
                            placeholder="Enter street address"
                        />
                        @error('address')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="city"
                            label="City"
                            placeholder="Enter city"
                            required
                        />
                        @error('city')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="state"
                            label="State/Province"
                            placeholder="Enter state or province"
                        />
                        @error('state')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="postal_code"
                            label="Postal Code"
                            placeholder="Enter postal code"
                        />
                        @error('postal_code')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="country"
                            label="Country"
                            placeholder="Enter country"
                        />
                        @error('country')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Settings & Description -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Settings & Description</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select
                            wire:model="status"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:textarea
                            wire:model="description"
                            label="Description"
                            placeholder="Enter any additional information about this branch"
                            rows="4"
                        />
                        @error('description')
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
                    {{ $isEditing ? 'Update Branch' : 'Create Branch' }}
                </flux:button>
            </div>
        </form>
    </div>
</flux:main>
