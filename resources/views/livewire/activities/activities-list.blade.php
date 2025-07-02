<div>
    <flux:header>
        <flux:heading size="lg">{{ __('Activities') }}</flux:heading>

        <flux:spacer />

        @if(auth()->user()->can('create activities'))
            <flux:button :href="route('activities.create')" wire:navigate icon="plus">
                Add Activity
            </flux:button>
        @endif
    </flux:header>

    <div class="space-y-6">
        {{-- Display messages --}}
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="text-green-800">{{ session('message') }}</div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="text-red-800">{{ session('error') }}</div>
            </div>
        @endif

        <!-- Search and Filters -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <div class="sm:col-span-2 lg:col-span-2">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search activities..."
                    icon="magnifying-glass"
                />
            </div>

            <div>
                <select
                    wire:model.live="typeFilter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Types</option>
                    <option value="call">Call</option>
                    <option value="email">Email</option>
                    <option value="meeting">Meeting</option>
                    <option value="note">Note</option>
                    <option value="task">Task</option>
                </select>
            </div>

            <div>
                <select
                    wire:model.live="statusFilter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div>
                <select
                    wire:model.live="dateFilter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Dates</option>
                    <option value="overdue">Overdue</option>
                    <option value="today">Due Today</option>
                    <option value="upcoming">Upcoming (7 days)</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                </select>
            </div>

            <div>
                <select
                    wire:model.live="relatedFilter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Related</option>
                    <option value="App\Models\Lead">Leads</option>
                    <option value="App\Models\Opportunity">Opportunities</option>
                    <option value="App\Models\Customer">Customers</option>
                </select>
            </div>
        </div>

        <!-- Desktop Table View (hidden on mobile/tablet) -->
        <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Activity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Related To</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Scheduled</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assigned To</th>
                            @if(auth()->user()->can('edit activities'))
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($activities as $activity)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $activity->is_overdue ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $activity->subject }}
                                        </div>
                                        @if($activity->description)
                                            <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                                {{ Str::limit($activity->description, 50) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge :color="$activity->type_color" size="sm">
                                        {{ ucfirst($activity->type) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($activity->related)
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ class_basename($activity->related_type) }}: {{ $activity->related->name ?? $activity->related->title ?? 'Untitled' }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">No relation</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($activity->scheduled_at)
                                        <div class="{{ $activity->is_overdue ? 'text-red-600 dark:text-red-400 font-medium' : '' }}">
                                            {{ $activity->scheduled_at->format('M j, Y') }}
                                        </div>
                                        <div class="text-xs {{ $activity->is_overdue ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                                            {{ $activity->scheduled_at->format('g:i A') }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">Not scheduled</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge :color="$activity->status_color" size="sm">
                                        {{ ucfirst($activity->status) }}
                                    </flux:badge>
                                    @if($activity->outcome)
                                        <div class="mt-1">
                                            <flux:badge :color="$activity->outcome_color" size="sm">
                                                {{ ucfirst(str_replace('_', ' ', $activity->outcome)) }}
                                            </flux:badge>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $activity->user->name }}
                                </td>
                                @if(auth()->user()->can('edit activities'))
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            @if(auth()->user()->canManageAllBranches() || $activity->user_id === auth()->user()->id)
                                                <flux:button
                                                    :href="route('activities.edit', $activity)"
                                                    variant="ghost"
                                                    size="sm"
                                                    wire:navigate
                                                >
                                                    Edit
                                                </flux:button>
                                            @endif
                                            @if($activity->status === 'pending' && (auth()->user()->canManageAllBranches() || $activity->user_id === auth()->user()->id))
                                                <flux:button
                                                    wire:click="markAsCompleted({{ $activity->id }}, 'successful')"
                                                    variant="ghost"
                                                    size="sm"
                                                    color="green"
                                                    wire:confirm="Mark this activity as completed?"
                                                >
                                                    Complete
                                                </flux:button>
                                                <flux:button
                                                    wire:click="markAsCancelled({{ $activity->id }})"
                                                    variant="ghost"
                                                    size="sm"
                                                    color="red"
                                                    wire:confirm="Cancel this activity?"
                                                >
                                                    Cancel
                                                </flux:button>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('edit activities') ? '7' : '6' }}" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    @if($search)
                                        No activities found matching "{{ $search }}"
                                    @else
                                        No activities found.
                                        @if(auth()->user()->can('create activities'))
                                            <flux:link :href="route('activities.create')" wire:navigate class="text-blue-600 hover:underline">Create your first activity</flux:link>
                                        @endif
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
            @forelse($activities as $activity)
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm {{ $activity->is_overdue ? 'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/10' : '' }}">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $activity->subject }}
                            </h3>
                            @if($activity->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ Str::limit($activity->description, 100) }}
                                </p>
                            @endif
                        </div>
                        <div class="flex flex-col gap-2">
                            <flux:badge :color="$activity->type_color" size="sm">
                                {{ ucfirst($activity->type) }}
                            </flux:badge>
                            <flux:badge :color="$activity->status_color" size="sm">
                                {{ ucfirst($activity->status) }}
                            </flux:badge>
                        </div>
                    </div>

                    <!-- Related Info -->
                    @if($activity->related)
                        <div class="mb-3">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ class_basename($activity->related_type) }}: {{ $activity->related->name ?? $activity->related->title ?? 'Untitled' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Schedule and Assignment -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Scheduled</div>
                            @if($activity->scheduled_at)
                                <div class="text-sm {{ $activity->is_overdue ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-900 dark:text-gray-100' }}">
                                    {{ $activity->scheduled_at->format('M j, Y') }}
                                </div>
                                <div class="text-xs {{ $activity->is_overdue ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ $activity->scheduled_at->format('g:i A') }}
                                </div>
                            @else
                                <div class="text-sm text-gray-400 dark:text-gray-500">Not scheduled</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Assigned To</div>
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $activity->user->name }}
                            </div>
                        </div>
                    </div>

                    <!-- Outcome -->
                    @if($activity->outcome)
                        <div class="mb-4">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Outcome</div>
                            <flux:badge :color="$activity->outcome_color" size="sm">
                                {{ ucfirst(str_replace('_', ' ', $activity->outcome)) }}
                            </flux:badge>
                        </div>
                    @endif

                    <!-- Actions -->
                    @if(auth()->user()->can('edit activities') && (
                        (auth()->user()->canManageAllBranches() || $activity->user_id === auth()->user()->id) ||
                        ($activity->status === 'pending' && (auth()->user()->canManageAllBranches() || $activity->user_id === auth()->user()->id))
                    ))
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                @if(auth()->user()->canManageAllBranches() || $activity->user_id === auth()->user()->id)
                                    <flux:button
                                        :href="route('activities.edit', $activity)"
                                        variant="ghost"
                                        size="sm"
                                        wire:navigate
                                    >
                                        Edit
                                    </flux:button>
                                @endif
                            </div>
                            @if($activity->status === 'pending' && (auth()->user()->canManageAllBranches() || $activity->user_id === auth()->user()->id))
                                <div class="flex items-center gap-2">
                                    <flux:button
                                        wire:click="markAsCompleted({{ $activity->id }}, 'successful')"
                                        variant="ghost"
                                        size="sm"
                                        color="green"
                                        wire:confirm="Mark this activity as completed?"
                                    >
                                        Complete
                                    </flux:button>
                                    <flux:button
                                        wire:click="markAsCancelled({{ $activity->id }})"
                                        variant="ghost"
                                        size="sm"
                                        color="red"
                                        wire:confirm="Cancel this activity?"
                                    >
                                        Cancel
                                    </flux:button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 text-center">
                    <div class="text-gray-500 dark:text-gray-400">
                        @if($search)
                            No activities found matching "{{ $search }}"
                        @else
                            No activities found.
                            @if(auth()->user()->can('create activities'))
                                <flux:link :href="route('activities.create')" wire:navigate class="text-blue-600 hover:underline">Create your first activity</flux:link>
                            @endif
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($activities->hasPages())
            <div class="mt-6">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
