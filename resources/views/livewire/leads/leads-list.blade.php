<div>
    <flux:header>
        <flux:heading size="lg">{{ __('Leads') }}</flux:heading>

        <flux:spacer />

        <flux:button :href="route('leads.create')" wire:navigate icon="plus">
            Add Lead
        </flux:button>
    </flux:header>

    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="flex flex-col lg:flex-row gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search leads, customers..."
                icon="magnifying-glass"
                class="flex-1"
            />

            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-48">
                    <select
                        wire:model.live="statusFilter"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">All Statuses</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-48">
                    <select
                        wire:model.live="priorityFilter"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">All Priorities</option>
                        @foreach($priorities as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-48">
                    <select
                        wire:model.live="assignedUserFilter"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($search || $statusFilter || $priorityFilter || $assignedUserFilter)
                    <flux:button wire:click="clearFilters" variant="ghost" size="sm">
                        Clear Filters
                    </flux:button>
                @endif
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Desktop Table View (hidden on mobile/tablet) -->
        <div class="hidden xl:block bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('title')">
                                <div class="flex items-center space-x-1">
                                    <span>Lead Title</span>
                                    @if($sortField === 'title')
                                        <flux:icon name="chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('status')">
                                <div class="flex items-center space-x-1">
                                    <span>Status</span>
                                    @if($sortField === 'status')
                                        <flux:icon name="chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('priority')">
                                <div class="flex items-center space-x-1">
                                    <span>Priority</span>
                                    @if($sortField === 'priority')
                                        <flux:icon name="chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assigned To</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('follow_up_date')">
                                <div class="flex items-center space-x-1">
                                    <span>Follow-up</span>
                                    @if($sortField === 'follow_up_date')
                                        <flux:icon name="chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('estimated_value')">
                                <div class="flex items-center space-x-1">
                                    <span>Value</span>
                                    @if($sortField === 'estimated_value')
                                        <flux:icon name="chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($leads as $lead)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $lead->title }}</div>
                                    @if($lead->description)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($lead->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $lead->customer->name ?? 'No Customer' }}</div>
                                    @if($lead->customer)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $lead->customer->mobile ?? $lead->customer->email ?? '' }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge
                                        :color="$lead->status === 'new' ? 'blue' : ($lead->status === 'contacted' ? 'yellow' : ($lead->status === 'qualified' ? 'green' : ($lead->status === 'converted' ? 'purple' : 'red')))"
                                        size="sm"
                                    >
                                        {{ ucfirst($lead->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge
                                        :color="$lead->priority === 'high' ? 'red' : ($lead->priority === 'medium' ? 'yellow' : 'green')"
                                        size="sm"
                                    >
                                        {{ ucfirst($lead->priority) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $lead->assignedUser->name ?? 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($lead->follow_up_date)
                                        <div class="{{ $lead->is_overdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                                            {{ $lead->follow_up_date->format('M j, Y') }}
                                        </div>
                                        @if($lead->is_overdue)
                                            <div class="text-xs text-red-500 dark:text-red-400">Overdue</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">Not set</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    @if($lead->estimated_value)
                                        ${{ number_format($lead->estimated_value, 2) }}
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <flux:button
                                            :href="route('leads.edit', $lead)"
                                            variant="ghost"
                                            size="sm"
                                            wire:navigate
                                        >
                                            Edit
                                        </flux:button>

                                        @if($lead->status !== 'converted')
                                            <flux:button
                                                wire:click="convertToOpportunity({{ $lead->id }})"
                                                variant="ghost"
                                                size="sm"
                                                color="green"
                                            >
                                                Convert
                                            </flux:button>
                                        @endif

                                        <flux:button
                                            wire:click="deleteLead({{ $lead->id }})"
                                            variant="ghost"
                                            size="sm"
                                            color="red"
                                            wire:confirm="Are you sure you want to delete this lead?"
                                        >
                                            Delete
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    @if($search || $statusFilter || $priorityFilter || $assignedUserFilter)
                                        No leads found matching your criteria.
                                    @else
                                        No leads found. <flux:link :href="route('leads.create')" wire:navigate class="text-blue-600 hover:underline">Create your first lead</flux:link>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile/Tablet Card View (visible on mobile/tablet) -->
        <div class="xl:hidden space-y-4">
            @forelse($leads as $lead)
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                    <!-- Lead Title and Status -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">
                                {{ $lead->title }}
                            </h3>
                            @if($lead->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ $lead->description }}
                                </p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2 ml-3">
                            <flux:badge
                                :color="$lead->status === 'new' ? 'blue' : ($lead->status === 'contacted' ? 'yellow' : ($lead->status === 'qualified' ? 'green' : ($lead->status === 'converted' ? 'purple' : 'red')))"
                                size="sm"
                            >
                                {{ ucfirst($lead->status) }}
                            </flux:badge>
                            <flux:badge
                                :color="$lead->priority === 'high' ? 'red' : ($lead->priority === 'medium' ? 'yellow' : 'green')"
                                size="sm"
                            >
                                {{ ucfirst($lead->priority) }}
                            </flux:badge>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    @if($lead->customer)
                        <div class="flex items-center mb-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $lead->customer->name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $lead->customer->mobile ?? $lead->customer->email ?? 'No contact info' }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Lead Details Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <!-- Assigned To -->
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assigned To</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100 mt-1">
                                {{ $lead->assignedUser->name ?? 'Unassigned' }}
                            </dd>
                        </div>

                        <!-- Estimated Value -->
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Value</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100 mt-1">
                                @if($lead->estimated_value)
                                    ${{ number_format($lead->estimated_value, 2) }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Not set</span>
                                @endif
                            </dd>
                        </div>

                        <!-- Follow-up Date -->
                        <div class="col-span-2">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Follow-up Date</dt>
                            <dd class="text-sm mt-1">
                                @if($lead->follow_up_date)
                                    <div class="flex items-center">
                                        <span class="{{ $lead->is_overdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                                            {{ $lead->follow_up_date->format('M j, Y') }}
                                        </span>
                                        @if($lead->is_overdue)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200">
                                                Overdue
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Not set</span>
                                @endif
                            </dd>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-200 dark:border-gray-600">
                        <flux:button
                            :href="route('leads.edit', $lead)"
                            variant="filled"
                            size="sm"
                            wire:navigate
                            class="flex-1 sm:flex-none"
                        >
                            Edit
                        </flux:button>

                        @if($lead->status !== 'converted')
                            <flux:button
                                wire:click="convertToOpportunity({{ $lead->id }})"
                                variant="filled"
                                size="sm"
                                color="green"
                                class="flex-1 sm:flex-none"
                            >
                                Convert
                            </flux:button>
                        @endif

                        <flux:button
                            wire:click="deleteLead({{ $lead->id }})"
                            variant="ghost"
                            size="sm"
                            color="red"
                            wire:confirm="Are you sure you want to delete this lead?"
                            class="flex-1 sm:flex-none"
                        >
                            Delete
                        </flux:button>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 text-center">
                    <div class="text-gray-500 dark:text-gray-400">
                        @if($search || $statusFilter || $priorityFilter || $assignedUserFilter)
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.881-6.08-2.33"/>
                            </svg>
                            <p class="text-lg font-medium mb-2">No leads found</p>
                            <p class="text-sm">Try adjusting your search criteria or filters.</p>
                        @else
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <p class="text-lg font-medium mb-2">No leads yet</p>
                            <p class="text-sm mb-4">Get started by creating your first lead.</p>
                            <flux:button :href="route('leads.create')" wire:navigate>
                                Create Lead
                            </flux:button>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($leads->hasPages())
            <div class="mt-6">
                {{ $leads->links() }}
            </div>
        @endif
    </div>
</div>
