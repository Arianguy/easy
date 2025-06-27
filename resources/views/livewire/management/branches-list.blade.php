<flux:main>
    <flux:header>
        <flux:heading size="lg">{{ __('Branch Management') }}</flux:heading>

        <flux:spacer />

        <flux:button :href="route('branches.create')" wire:navigate icon="plus">
            Add Branch
        </flux:button>
    </flux:header>

    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="flex flex-col sm:flex-row gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search branches..."
                icon="magnifying-glass"
                class="flex-1"
            />

            <div class="w-full sm:w-48">
                <select
                    wire:model.live="statusFilter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Branches Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Branch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Users</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($branches as $branch)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $branch->name }}
                                            </div>
                                            @if($branch->code)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">Code: {{ $branch->code }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        @if($branch->address)
                                            {{ $branch->address }}<br>
                                        @endif
                                        {{ $branch->city }}
                                        @if($branch->state), {{ $branch->state }}@endif
                                    </div>
                                    @if($branch->phone)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $branch->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $branch->users_count }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">users</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $branch->customers_count }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">customers</div>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge
                                        :color="$branch->status === 'active' ? 'green' : 'red'"
                                        size="sm"
                                    >
                                        {{ ucfirst($branch->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $branch->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <flux:button
                                            :href="route('branches.edit', $branch)"
                                            variant="ghost"
                                            size="sm"
                                            wire:navigate
                                        >
                                            Edit
                                        </flux:button>
                                        <flux:button
                                            wire:click="toggleStatus({{ $branch->id }})"
                                            variant="ghost"
                                            size="sm"
                                            :color="$branch->status === 'active' ? 'red' : 'green'"
                                        >
                                            {{ $branch->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    @if($search)
                                        No branches found matching "{{ $search }}"
                                    @else
                                        No branches found. <flux:link :href="route('branches.create')" wire:navigate class="text-blue-600 hover:underline">Create your first branch</flux:link>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($branches->hasPages())
            <div class="mt-6">
                {{ $branches->links() }}
            </div>
        @endif
    </div>
</flux:main>
