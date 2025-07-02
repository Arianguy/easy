<div>
    <flux:header>
        <flux:heading size="lg">{{ $isEditing ? 'Edit Customer' : 'Add Customer' }}</flux:heading>

        <flux:spacer />

        <flux:button wire:click="cancel" variant="ghost">
            Cancel
        </flux:button>
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
                            wire:model.live="mobile"
                            label="Mobile"
                            placeholder="+91 Enter 10-digit mobile number"
                            required
                        />
                        @if($mobileCheckMessage)
                            <div class="mt-1 text-sm {{ $mobileExists ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400' }}">
                                {{ $mobileCheckMessage }}
                            </div>
                        @endif
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

                    <!-- Customer Interests -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Customer Interests
                        </label>
                        <div class="space-y-2">
                            <div class="flex flex-wrap gap-2">
                                @foreach($allInterests as $interest)
                                    <label class="inline-flex items-center cursor-pointer interest-tag"
                                           data-interest-id="{{ $interest->id }}"
                                           data-interest-color="{{ $interest->color }}">
                                        <input
                                            type="checkbox"
                                            wire:model.live="customer_interests"
                                            value="{{ $interest->id }}"
                                            class="sr-only interest-checkbox"
                                            id="interest_{{ $interest->id }}"
                                        >
                                        <span class="px-3 py-1 rounded-full text-sm font-medium border-2 transition-all duration-200 select-none interest-span
                                            @if(in_array($interest->id, $customer_interests))
                                                text-white
                                            @else
                                                text-gray-700 hover:border-gray-400 dark:text-gray-300 dark:hover:border-gray-500
                                            @endif"
                                            style="@if(in_array($interest->id, $customer_interests))
                                                background-color: {{ $interest->color }}; border-color: {{ $interest->color }};
                                            @else
                                                border-color: #d1d5db;
                                            @endif">
                                            {{ $interest->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Select multiple interests that apply to this customer</p>
                            @if(count($customer_interests) > 0)
                                <div class="text-xs text-blue-600 dark:text-blue-400">
                                    Selected: {{ count($customer_interests) }} interest(s)
                                </div>
                            @endif

                            {{-- Debug info --}}
                            @if(config('app.debug'))
                                <div class="text-xs text-gray-400 mt-1">
                                    Debug: {{ json_encode($customer_interests) }}
                                </div>
                            @endif
                        </div>
                        @error('customer_interests')
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
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $isEditing ? 'Update Customer' : 'Create Customer' }}</span>
                    <span wire:loading>Saving...</span>
                </flux:button>
            </div>

            {{-- Debug info --}}
            @if(config('app.debug'))
                <div class="mt-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-xs">
                    <strong>Debug Info:</strong><br>
                    Name: {{ $name }}<br>
                    Branch ID: {{ $branch_id }}<br>
                    Customer Interests: {{ json_encode($customer_interests) }}<br>
                    Is Editing: {{ $isEditing ? 'true' : 'false' }}
                </div>
            @endif
        </form>
    </div>

    <!-- Interest tag styles -->
    <style>
        /* Interest tag styles */
        .interest-tag {
            transition: transform 0.1s ease;
        }

        .interest-tag:hover {
            transform: scale(1.02);
        }

        .interest-tag:active {
            transform: scale(0.98);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced interest selection
            document.addEventListener('click', function(e) {
                if (e.target.closest('.interest-tag')) {
                    const tag = e.target.closest('.interest-tag');
                    const checkbox = tag.querySelector('.interest-checkbox');
                    const span = tag.querySelector('.interest-span');
                    const interestId = tag.dataset.interestId;
                    const interestColor = tag.dataset.interestColor;

                    // Toggle checkbox
                    checkbox.checked = !checkbox.checked;

                    // Trigger Livewire update
                    checkbox.dispatchEvent(new Event('input', { bubbles: true }));

                    // Update visual state immediately for better UX
                    if (checkbox.checked) {
                        span.style.backgroundColor = interestColor;
                        span.style.borderColor = interestColor;
                        span.style.color = 'white';
                        span.classList.remove('text-gray-700', 'hover:border-gray-400', 'dark:text-gray-300', 'dark:hover:border-gray-500');
                        span.classList.add('text-white');
                    } else {
                        span.style.backgroundColor = '';
                        span.style.borderColor = '#d1d5db';
                        span.style.color = '';
                        span.classList.remove('text-white');
                        span.classList.add('text-gray-700', 'hover:border-gray-400', 'dark:text-gray-300', 'dark:hover:border-gray-500');
                    }
                }
            });
        });

        // Listen for Livewire updates to refresh interest states
        document.addEventListener('livewire:updated', function() {
            // Update interest tag states after Livewire updates
            document.querySelectorAll('.interest-tag').forEach(function(tag) {
                const checkbox = tag.querySelector('.interest-checkbox');
                const span = tag.querySelector('.interest-span');
                const interestColor = tag.dataset.interestColor;

                if (checkbox.checked) {
                    span.style.backgroundColor = interestColor;
                    span.style.borderColor = interestColor;
                    span.style.color = 'white';
                    span.classList.remove('text-gray-700', 'hover:border-gray-400', 'dark:text-gray-300', 'dark:hover:border-gray-500');
                    span.classList.add('text-white');
                } else {
                    span.style.backgroundColor = '';
                    span.style.borderColor = '#d1d5db';
                    span.style.color = '';
                    span.classList.remove('text-white');
                    span.classList.add('text-gray-700', 'hover:border-gray-400', 'dark:text-gray-300', 'dark:hover:border-gray-500');
                }
            });
        });
    </script>
</div>
