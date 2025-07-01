<div>
    <flux:header>
        <flux:heading size="lg">{{ $isEditing ? 'Edit Activity' : 'Create Activity' }}</flux:heading>
    </flux:header>

    <div class="max-w-4xl">
        {{-- Display general errors --}}
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="text-red-800">{{ session('error') }}</div>
            </div>
        @endif

        {{-- Display validation errors --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="text-red-800 font-medium mb-2">Please fix the following errors:</div>
                <ul class="list-disc list-inside text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit="save" class="space-y-8">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Basic Information</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Activity Type</label>
                        <select
                            wire:model="type"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                            <option value="call">Call</option>
                            <option value="email">Email</option>
                            <option value="meeting">Meeting</option>
                            <option value="note">Note</option>
                            <option value="task">Task</option>
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
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        @error('status')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:input
                            wire:model="subject"
                            label="Subject"
                            placeholder="Enter activity subject"
                            required
                        />
                        @error('subject')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <flux:textarea
                            wire:model="description"
                            label="Description"
                            placeholder="Enter activity description (optional)"
                            rows="4"
                        />
                        @error('description')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Scheduling & Duration -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Scheduling & Duration</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Scheduled Date & Time</label>
                        <input
                            type="datetime-local"
                            wire:model="scheduled_at"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                        />
                        @error('scheduled_at')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <flux:input
                            wire:model="duration_minutes"
                            label="Duration (minutes)"
                            type="number"
                            min="1"
                            max="1440"
                            placeholder="e.g., 30"
                        />
                        @error('duration_minutes')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Related To -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Related To</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Related Type</label>
                        <select
                            wire:model.live="related_type"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Select Type</option>
                            <option value="App\Models\Lead">Lead</option>
                            <option value="App\Models\Opportunity">Opportunity</option>
                            <option value="App\Models\Customer">Customer</option>
                        </select>
                        @error('related_type')
                            <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($related_type)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select {{ class_basename($related_type) }}
                            </label>
                            <select
                                wire:model="related_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Select {{ class_basename($related_type) }}</option>
                                @if($related_type === 'App\Models\Lead')
                                    @foreach($leads as $lead)
                                        <option value="{{ $lead->id }}">
                                            {{ $lead->title }} - {{ $lead->customer->name ?? 'No Customer' }}
                                        </option>
                                    @endforeach
                                @elseif($related_type === 'App\Models\Opportunity')
                                    @foreach($opportunities as $opportunity)
                                        <option value="{{ $opportunity->id }}">
                                            {{ $opportunity->name }} - {{ $opportunity->lead->customer->name ?? 'No Customer' }}
                                        </option>
                                    @endforeach
                                @elseif($related_type === 'App\Models\Customer')
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->name }}{{ $customer->company ? ' (' . $customer->company . ')' : '' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('related_id')
                                <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>

            <!-- Outcome & Assignment -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="md" class="mb-6">Outcome & Assignment</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($status === 'completed')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Outcome</label>
                            <select
                                wire:model="outcome"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Select Outcome</option>
                                <option value="successful">Successful</option>
                                <option value="unsuccessful">Unsuccessful</option>
                                <option value="rescheduled">Rescheduled</option>
                                <option value="no_response">No Response</option>
                            </select>
                            @error('outcome')
                                <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="{{ $status === 'completed' ? '' : 'md:col-span-2' }}">
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
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $isEditing ? 'Update Activity' : 'Create Activity' }}</span>
                    <span wire:loading>Saving...</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
