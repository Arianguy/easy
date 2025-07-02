<div>
    <flux:header>
        <flux:heading size="lg">{{ __('Opportunities') }}</flux:heading>

        <flux:spacer />

        @if(auth()->user()->can('create opportunities'))
            <flux:button :href="route('opportunities.create')" wire:navigate icon="plus">
                Add Opportunity
            </flux:button>
        @endif
    </flux:header>

    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="flex flex-col sm:flex-row gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search opportunities..."
                icon="magnifying-glass"
                class="flex-1"
            />

            <div class="w-full sm:w-48">
                <select
                    wire:model.live="stageFilter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Stages</option>
                    <option value="prospecting">Prospecting</option>
                    <option value="proposal">Proposal</option>
                    <option value="negotiation">Negotiation</option>
                    <option value="won">Won</option>
                    <option value="lost">Lost</option>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Value</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Probability</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expected Close</th>
                            @if(auth()->user()->can('edit opportunities'))
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($opportunities as $opportunity)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $opportunity->is_overdue ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $opportunity->name }}
                                            </div>
                                            @if($opportunity->description)
                                                <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                                    {{ Str::limit($opportunity->description, 50) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($opportunity->customer)
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ $opportunity->customer->name }}
                                        </div>
                                        @if($opportunity->customer->company)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $opportunity->customer->company }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    @if($opportunity->value)
                                        ₹{{ number_format($opportunity->value, 2) }}
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge
                                        :color="$opportunity->stage_color"
                                        size="sm"
                                    >
                                        {{ ucfirst($opportunity->stage) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $opportunity->probability }}%
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($opportunity->expected_close_date)
                                        <div class="{{ $opportunity->is_overdue ? 'text-red-600 dark:text-red-400 font-medium' : '' }}">
                                            {{ $opportunity->expected_close_date->format('M j, Y') }}
                                        </div>
                                        @if($opportunity->is_overdue)
                                            <div class="text-xs text-red-500">
                                                Overdue by {{ abs($opportunity->days_until_close) }} days
                                            </div>
                                        @elseif($opportunity->days_until_close !== null && $opportunity->days_until_close >= 0)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $opportunity->days_until_close }} days left
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                @if(auth()->user()->can('edit opportunities'))
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <flux:button
                                                :href="route('opportunities.edit', $opportunity)"
                                                variant="ghost"
                                                size="sm"
                                                wire:navigate
                                            >
                                                Edit
                                            </flux:button>

                                            @if($opportunity->stage !== 'won' && $opportunity->stage !== 'lost')
                                                <flux:button
                                                    wire:click="markAsWon({{ $opportunity->id }})"
                                                    variant="ghost"
                                                    size="sm"
                                                    color="green"
                                                    wire:confirm="Mark this opportunity as won?"
                                                >
                                                    Won
                                                </flux:button>
                                                <flux:button
                                                    wire:click="markAsLost({{ $opportunity->id }})"
                                                    variant="ghost"
                                                    size="sm"
                                                    color="red"
                                                    wire:confirm="Mark this opportunity as lost?"
                                                >
                                                    Lost
                                                </flux:button>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('edit opportunities') ? '7' : '6' }}" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    @if($search)
                                        No opportunities found matching "{{ $search }}"
                                    @else
                                        No opportunities found. <flux:link :href="route('opportunities.create')" wire:navigate class="text-blue-600 hover:underline">Create your first opportunity</flux:link>
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
            @forelse($opportunities as $opportunity)
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm {{ $opportunity->is_overdue ? 'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/10' : '' }}">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $opportunity->name }}
                            </h3>
                            @if($opportunity->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ Str::limit($opportunity->description, 80) }}
                                </p>
                            @endif
                        </div>
                        <flux:badge
                            :color="$opportunity->stage_color"
                            size="sm"
                        >
                            {{ ucfirst($opportunity->stage) }}
                        </flux:badge>
                    </div>

                    <!-- Customer Info -->
                    @if($opportunity->customer)
                        <div class="mb-3">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $opportunity->customer->name }}</div>
                                    @if($opportunity->customer->company)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $opportunity->customer->company }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Value and Probability -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Value</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                @if($opportunity->value)
                                    ₹{{ number_format($opportunity->value, 0) }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Probability</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $opportunity->probability }}%
                            </div>
                        </div>
                    </div>

                    <!-- Expected Close Date -->
                    @if($opportunity->expected_close_date)
                        <div class="mb-4">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Expected Close</div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <div class="text-sm {{ $opportunity->is_overdue ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-900 dark:text-gray-100' }}">
                                        {{ $opportunity->expected_close_date->format('M j, Y') }}
                                    </div>
                                    @if($opportunity->is_overdue)
                                        <div class="text-xs text-red-500">
                                            Overdue by {{ abs($opportunity->days_until_close) }} days
                                        </div>
                                    @elseif($opportunity->days_until_close !== null && $opportunity->days_until_close >= 0)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $opportunity->days_until_close }} days left
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    @if(auth()->user()->can('edit opportunities'))
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <flux:button
                                    :href="route('opportunities.edit', $opportunity)"
                                    variant="ghost"
                                    size="sm"
                                    wire:navigate
                                >
                                    Edit
                                </flux:button>
                            </div>
                            @if($opportunity->stage !== 'won' && $opportunity->stage !== 'lost')
                                <div class="flex items-center gap-2">
                                    <flux:button
                                        wire:click="markAsWon({{ $opportunity->id }})"
                                        variant="ghost"
                                        size="sm"
                                        color="green"
                                        wire:confirm="Mark this opportunity as won?"
                                    >
                                        Won
                                    </flux:button>
                                    <flux:button
                                        wire:click="markAsLost({{ $opportunity->id }})"
                                        variant="ghost"
                                        size="sm"
                                        color="red"
                                        wire:confirm="Mark this opportunity as lost?"
                                    >
                                        Lost
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
                            No opportunities found matching "{{ $search }}"
                        @else
                            No opportunities found. <flux:link :href="route('opportunities.create')" wire:navigate class="text-blue-600 hover:underline">Create your first opportunity</flux:link>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($opportunities->hasPages())
            <div class="mt-6">
                {{ $opportunities->links() }}
            </div>
        @endif
    </div>
</div>
