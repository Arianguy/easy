<div>
    <flux:header>
        <flux:heading size="lg">{{ __('Customers') }}</flux:heading>

        <flux:spacer />

        <flux:button :href="route('customers.create')" wire:navigate icon="plus">
            Add Customer
        </flux:button>
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

        <!-- Customers Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
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

        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="mt-6">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
