<div>
    <flux:header>
        <flux:heading size="lg">{{ __('Customers') }}</flux:heading>

        <flux:spacer />

        @if(auth()->user()->can('create customers'))
            <flux:button :href="route('customers.create')" wire:navigate icon="plus">
                Add Customer
            </flux:button>
        @endif
    </flux:header>

    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="flex flex-col sm:flex-row gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search customers..."
                icon="magnifying-glass"
                class="flex-1"
            />

            <div class="w-full sm:w-48">
                <select
                    wire:model.live="statusFilter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Statuses</option>
                    <option value="potential">Potential</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Desktop Table View (hidden on mobile/tablet) -->
        <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Interests</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                            @if(auth()->user()->can('edit customers'))
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $customer->name }}
                                            </div>
                                            @if($customer->company)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $customer->company }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $customer->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($customer->mobile)
                                        {{ $customer->mobile }} (M)
                                    @elseif($customer->phone)
                                        {{ $customer->phone }} (P)
                                    @else
                                        No contact
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($customer->interests && is_string($customer->interests) && !empty($customer->interests))
                                        {{-- Legacy string interests --}}
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($customer->interests, 30) }}</span>
                                    @elseif($customer->interests && !is_string($customer->interests) && $customer->interests->count() > 0)
                                        {{-- New relationship interests --}}
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($customer->interests->take(3) as $interest)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                                      style="background-color: {{ $interest->color }}20; color: {{ $interest->color }};">
                                                    {{ $interest->name }}
                                                </span>
                                            @endforeach
                                            @if($customer->interests->count() > 3)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                    +{{ $customer->interests->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">No interests</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge
                                        :color="$customer->status === 'active' ? 'green' : ($customer->status === 'potential' ? 'yellow' : 'red')"
                                        size="sm"
                                    >
                                        {{ ucfirst($customer->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $customer->created_at->format('M j, Y') }}
                                </td>
                                @if(auth()->user()->can('edit customers'))
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <flux:button
                                                :href="route('customers.edit', $customer)"
                                                variant="ghost"
                                                size="sm"
                                                wire:navigate
                                            >
                                                Edit
                                            </flux:button>
                                            <flux:button
                                                wire:click="toggleStatus({{ $customer->id }})"
                                                variant="ghost"
                                                size="sm"
                                                :color="$customer->status === 'active' ? 'red' : 'green'"
                                            >
                                                {{ $customer->status === 'active' ? 'Deactivate' : 'Activate' }}
                                            </flux:button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('edit customers') ? '7' : '6' }}" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    @if($search)
                                        No customers found matching "{{ $search }}"
                                    @else
                                        No customers found. <flux:link :href="route('customers.create')" wire:navigate class="text-blue-600 hover:underline">Create your first customer</flux:link>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile/Tablet Card View (visible on mobile/tablet) -->
        <div class="lg:hidden space-y-4">
            @forelse($customers as $customer)
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $customer->name }}
                            </h3>
                            @if($customer->company)
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $customer->company }}</p>
                            @endif
                        </div>
                        <flux:badge
                            :color="$customer->status === 'active' ? 'green' : ($customer->status === 'potential' ? 'yellow' : 'red')"
                            size="sm"
                        >
                            {{ ucfirst($customer->status) }}
                        </flux:badge>
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-2 mb-4">
                        @if($customer->email)
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $customer->email }}
                            </div>
                        @endif
                        @if($customer->mobile || $customer->phone)
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                @if($customer->mobile)
                                    {{ $customer->mobile }} (Mobile)
                                @else
                                    {{ $customer->phone }} (Phone)
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Interests -->
                    @if($customer->interests && is_string($customer->interests) && !empty($customer->interests))
                        <div class="mb-4">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Interests</div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $customer->interests }}</p>
                        </div>
                    @elseif($customer->interests && !is_string($customer->interests) && $customer->interests->count() > 0)
                        <div class="mb-4">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Interests</div>
                            <div class="flex flex-wrap gap-1">
                                @foreach($customer->interests->take(4) as $interest)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                          style="background-color: {{ $interest->color }}20; color: {{ $interest->color }};">
                                        {{ $interest->name }}
                                    </span>
                                @endforeach
                                @if($customer->interests->count() > 4)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                        +{{ $customer->interests->count() - 4 }} more
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Footer -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Created {{ $customer->created_at->format('M j, Y') }}
                        </div>
                        @if(auth()->user()->can('edit customers'))
                            <div class="flex items-center gap-2">
                                <flux:button
                                    :href="route('customers.edit', $customer)"
                                    variant="ghost"
                                    size="sm"
                                    wire:navigate
                                >
                                    Edit
                                </flux:button>
                                <flux:button
                                    wire:click="toggleStatus({{ $customer->id }})"
                                    variant="ghost"
                                    size="sm"
                                    :color="$customer->status === 'active' ? 'red' : 'green'"
                                >
                                    {{ $customer->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </flux:button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 text-center">
                    <div class="text-gray-500 dark:text-gray-400">
                        @if($search)
                            No customers found matching "{{ $search }}"
                        @else
                            No customers found. <flux:link :href="route('customers.create')" wire:navigate class="text-blue-600 hover:underline">Create your first customer</flux:link>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="mt-6">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
