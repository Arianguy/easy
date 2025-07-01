<div>
    <flux:header>
        <flux:heading size="lg">{{ $isEditing ? 'Edit Campaign' : 'Add Campaign' }}</flux:heading>

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
                            label="Campaign Name"
                            placeholder="Enter campaign name"
                            required
                        />
                        @error('name')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:textarea
                            wire:model="description"
                            label="Description"
                            placeholder="Enter campaign description"
                            rows="4"
                        />
                        @error('description')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Campaign Type</label>
                        <select
                            wire:model="type"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                            <option value="email">Email</option>
                            <option value="social_media">Social Media</option>
                            <option value="print">Print</option>
                            <option value="radio">Radio</option>
                            <option value="tv">TV</option>
                            <option value="online">Online</option>
                            <option value="direct_mail">Direct Mail</option>
                            <option value="event">Event</option>
                            <option value="other">Other</option>
                        </select>
                        @error('type')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select
                            wire:model="status"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="paused">Paused</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        @error('status')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Timeline & Budget -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Timeline & Budget</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:input
                            wire:model="start_date"
                            label="Start Date"
                            type="date"
                            required
                        />
                        @error('start_date')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="end_date"
                            label="End Date"
                            type="date"
                        />
                        @error('end_date')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="budget"
                            label="Budget"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="Enter campaign budget"
                        />
                        @error('budget')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="actual_cost"
                            label="Actual Cost"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="Enter actual cost spent"
                        />
                        @error('actual_cost')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Audience & Targeting -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Audience & Targeting</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <flux:textarea
                            wire:model="target_audience"
                            label="Target Audience"
                            placeholder="Describe your target audience"
                            rows="3"
                        />
                        @error('target_audience')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="reached_audience"
                            label="Reached Audience"
                            type="number"
                            min="0"
                            placeholder="Number of people reached"
                        />
                        @error('reached_audience')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Performance Metrics</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:input
                            wire:model="leads_generated"
                            label="Leads Generated"
                            type="number"
                            min="0"
                            placeholder="Number of leads generated"
                        />
                        @error('leads_generated')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="conversions"
                            label="Conversions"
                            type="number"
                            min="0"
                            placeholder="Number of conversions"
                        />
                        @error('conversions')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:textarea
                            wire:model="metrics"
                            label="Additional Metrics"
                            placeholder="Enter additional metrics in JSON format (optional)"
                            rows="4"
                        />
                        @error('metrics')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Example: {"clicks": 1500, "impressions": 25000, "ctr": 6.0}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings & Assignment -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Settings & Assignment</flux:heading>

                <div class="grid grid-cols-1 gap-6">
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
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-4">
                <flux:button type="button" wire:click="cancel" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $isEditing ? 'Update Campaign' : 'Create Campaign' }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
