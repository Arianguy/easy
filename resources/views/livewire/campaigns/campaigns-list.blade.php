<div>
    <flux:header>
        <flux:heading size="lg">{{ __('Campaigns') }}</flux:heading>

        <flux:spacer />

        <flux:button :href="route('campaigns.create')" wire:navigate icon="plus">
            Add Campaign
        </flux:button>
    </flux:header>

    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="flex flex-col sm:flex-row gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search campaigns..."
                icon="magnifying-glass"
                class="flex-1"
            />

            <div class="w-full sm:w-48">
                <select
                    wire:model.live="statusFilter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="active">Active</option>
                    <option value="paused">Paused</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div class="w-full sm:w-48">
                <select
                    wire:model.live="typeFilter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Types</option>
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
            </div>
        </div>

        <!-- Desktop Table View (hidden on mobile/tablet) -->
        <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Campaign</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Budget</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Performance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($campaigns as $campaign)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $campaign->name }}
                                        </div>
                                        @if($campaign->description)
                                            <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                                {{ Str::limit($campaign->description, 50) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge color="blue" size="sm">
                                        {{ ucfirst(str_replace('_', ' ', $campaign->type)) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge
                                        :color="$campaign->status_color"
                                        size="sm"
                                    >
                                        {{ ucfirst($campaign->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div>{{ $campaign->start_date->format('M j, Y') }}</div>
                                    @if($campaign->end_date)
                                        <div>to {{ $campaign->end_date->format('M j, Y') }}</div>
                                    @else
                                        <div class="text-xs text-gray-400">No end date</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($campaign->budget)
                                        <div>₹{{ number_format($campaign->budget, 0) }}</div>
                                        @if($campaign->actual_cost)
                                            <div class="text-xs">
                                                Spent: ₹{{ number_format($campaign->actual_cost, 0) }}
                                                ({{ $campaign->budget_utilization }}%)
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">No budget</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($campaign->leads_generated)
                                        <div>{{ $campaign->leads_generated }} leads</div>
                                        @if($campaign->conversions)
                                            <div class="text-xs">
                                                {{ $campaign->conversions }} conversions ({{ $campaign->conversion_rate }}%)
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">No data</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <flux:button
                                            :href="route('campaigns.edit', $campaign)"
                                            variant="ghost"
                                            size="sm"
                                            wire:navigate
                                        >
                                            Edit
                                        </flux:button>
                                        @if(in_array($campaign->status, ['active', 'paused']))
                                            <flux:button
                                                wire:click="toggleStatus({{ $campaign->id }})"
                                                variant="ghost"
                                                size="sm"
                                                :color="$campaign->status === 'active' ? 'yellow' : 'green'"
                                            >
                                                {{ $campaign->status === 'active' ? 'Pause' : 'Activate' }}
                                            </flux:button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    @if($search)
                                        No campaigns found matching "{{ $search }}"
                                    @else
                                        No campaigns found. <flux:link :href="route('campaigns.create')" wire:navigate class="text-blue-600 hover:underline">Create your first campaign</flux:link>
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
            @forelse($campaigns as $campaign)
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $campaign->name }}
                            </h3>
                            @if($campaign->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ Str::limit($campaign->description, 80) }}
                                </p>
                            @endif
                        </div>
                        <div class="flex flex-col gap-2">
                            <flux:badge
                                :color="$campaign->status_color"
                                size="sm"
                            >
                                {{ ucfirst($campaign->status) }}
                            </flux:badge>
                            <flux:badge color="blue" size="sm">
                                {{ ucfirst(str_replace('_', ' ', $campaign->type)) }}
                            </flux:badge>
                        </div>
                    </div>

                    <!-- Duration -->
                    <div class="mb-4">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $campaign->start_date->format('M j, Y') }}
                                    @if($campaign->end_date)
                                        - {{ $campaign->end_date->format('M j, Y') }}
                                    @endif
                                </div>
                                @if(!$campaign->end_date)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">No end date</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Budget and Performance -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Budget</div>
                            @if($campaign->budget)
                                <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    ₹{{ number_format($campaign->budget, 0) }}
                                </div>
                                @if($campaign->actual_cost)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Spent: ₹{{ number_format($campaign->actual_cost, 0) }} ({{ $campaign->budget_utilization }}%)
                                    </div>
                                @endif
                            @else
                                <div class="text-sm text-gray-400 dark:text-gray-500">No budget</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Performance</div>
                            @if($campaign->leads_generated)
                                <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $campaign->leads_generated }} leads
                                </div>
                                @if($campaign->conversions)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $campaign->conversions }} conversions ({{ $campaign->conversion_rate }}%)
                                    </div>
                                @endif
                            @else
                                <div class="text-sm text-gray-400 dark:text-gray-500">No data</div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <flux:button
                                :href="route('campaigns.edit', $campaign)"
                                variant="ghost"
                                size="sm"
                                wire:navigate
                            >
                                Edit
                            </flux:button>
                        </div>
                        @if(in_array($campaign->status, ['active', 'paused']))
                            <div class="flex items-center gap-2">
                                <flux:button
                                    wire:click="toggleStatus({{ $campaign->id }})"
                                    variant="ghost"
                                    size="sm"
                                    :color="$campaign->status === 'active' ? 'yellow' : 'green'"
                                >
                                    {{ $campaign->status === 'active' ? 'Pause' : 'Activate' }}
                                </flux:button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 text-center">
                    <div class="text-gray-500 dark:text-gray-400">
                        @if($search)
                            No campaigns found matching "{{ $search }}"
                        @else
                            No campaigns found. <flux:link :href="route('campaigns.create')" wire:navigate class="text-blue-600 hover:underline">Create your first campaign</flux:link>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($campaigns->hasPages())
            <div class="mt-6">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</div>
