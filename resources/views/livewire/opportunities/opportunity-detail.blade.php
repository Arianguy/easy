<div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900">
    <!-- Top Header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <div class="flex items-center space-x-4">
                <flux:button variant="ghost" :href="route('opportunities.index')" wire:navigate class="shrink-0">
                    <flux:icon.arrow-left class="w-4 h-4" />
                </flux:button>
                <div class="min-w-0">
                    <h1 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white truncate">{{ $opportunity->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        @if($opportunity->stage === 'prospecting')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                {{ ucfirst($opportunity->stage) }}
                            </span>
                        @elseif($opportunity->stage === 'proposal')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                {{ ucfirst($opportunity->stage) }}
                            </span>
                        @elseif($opportunity->stage === 'negotiation')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                {{ ucfirst($opportunity->stage) }}
                            </span>
                        @elseif($opportunity->stage === 'won')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                {{ ucfirst($opportunity->stage) }}
                            </span>
                        @elseif($opportunity->stage === 'lost')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                {{ ucfirst($opportunity->stage) }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                {{ ucfirst($opportunity->stage) }}
                            </span>
                        @endif
                        <span class="hidden sm:inline text-sm text-gray-500 dark:text-gray-400">•</span>
                        <span class="text-sm text-gray-600 dark:text-gray-300">
                            ₹{{ number_format($opportunity->value, 2) }}
                        </span>
                        <span class="hidden sm:inline text-sm text-gray-500 dark:text-gray-400">•</span>
                        <span class="text-sm text-gray-600 dark:text-gray-300">
                            {{ $opportunity->probability }}% probability
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <flux:button :href="route('opportunities.index')" wire:navigate variant="ghost" class="w-full sm:w-auto">
                    Cancel
                </flux:button>
                <flux:button wire:click="saveOpportunity" variant="filled" class="w-full sm:w-auto">
                    <flux:icon.check class="w-4 h-4 mr-2" />
                    Save Changes
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6">
        <nav class="flex space-x-4 sm:space-x-8 overflow-x-auto" role="tablist">
            <button
                wire:click="setActiveTab('overview')"
                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap @if($activeTab === 'overview') border-blue-500 text-blue-600 dark:text-blue-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 @endif"
                type="button"
                role="tab"
            >
                Overview
            </button>
            <button
                wire:click="setActiveTab('activities')"
                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap @if($activeTab === 'activities') border-blue-500 text-blue-600 dark:text-blue-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 @endif"
                type="button"
                role="tab"
            >
                Activity
                <flux:badge variant="neutral" size="sm" class="ml-2">{{ $activities->count() }}</flux:badge>
            </button>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
        <!-- Left Content -->
        <div class="flex-1 overflow-y-auto">
            @if($activeTab === 'overview')
                <!-- Overview Content -->
                <div class="p-4 sm:p-6 space-y-6">
                    <!-- Key Metrics -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Deal Value</div>
                            <flux:input wire:model="value" type="number" step="0.01" class="mt-1" />
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Stage</div>
                            <div class="mt-1 space-y-2">
                                <flux:select wire:model.live="stage">
                                    <option value="prospecting">Prospecting</option>
                                    <option value="proposal">Proposal</option>
                                    <option value="negotiation">Negotiation</option>
                                    <option value="won">Won</option>
                                    <option value="lost">Lost</option>
                                </flux:select>
                                                                <div class="flex items-center space-x-2">
                                    @if($stage === 'prospecting')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                            {{ ucfirst($stage) }}
                                        </span>
                                    @elseif($stage === 'proposal')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                            {{ ucfirst($stage) }}
                                        </span>
                                    @elseif($stage === 'negotiation')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                            {{ ucfirst($stage) }}
                                        </span>
                                    @elseif($stage === 'won')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            {{ ucfirst($stage) }}
                                        </span>
                                    @elseif($stage === 'lost')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            {{ ucfirst($stage) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                            {{ ucfirst($stage) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Probability</div>
                            <flux:input wire:model="probability" type="number" min="0" max="100" readonly class="mt-1 bg-gray-50 dark:bg-gray-700" />
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Auto-calculated based on stage</div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Weighted Value</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white mt-1">₹{{ number_format($opportunity->weighted_value, 2) }}</div>
                        </div>
                    </div>

                    <!-- Opportunity Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Deal Details</h3>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deal Name</label>
                                <flux:input wire:model="name" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Close Date</label>
                                <flux:input wire:model="expected_close_date" type="date" />
                            </div>

                            @if(in_array($stage, ['won', 'lost']))
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Actual Close Date</label>
                                    <flux:input wire:model="actual_close_date" type="date" />
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Associated Lead</label>
                                <p class="text-gray-900 dark:text-white">{{ $opportunity->lead->title }}</p>
                                @if($opportunity->lead->customer)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $opportunity->lead->customer->name }}</p>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deal Owner</label>
                                <p class="text-gray-900 dark:text-white">{{ $opportunity->creator->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $opportunity->branch->name }}</p>
                            </div>

                                                        <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Products/Services</label>
                                <flux:input wire:model="products_services" placeholder="Enter products or services (comma separated)" />
                            </div>

                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                                <flux:textarea wire:model="description" rows="3" placeholder="Enter opportunity description..." />
                            </div>

                            @if($stage === 'lost')
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Reason for Lost <span class="text-red-500">*</span>
                                    </label>
                                    <flux:textarea wire:model="close_reason" rows="2" placeholder="Please explain why this opportunity was lost..." />
                                    <flux:error name="close_reason" />
                                </div>
                            @endif

                            @if($stage === 'won')
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Reason for Won
                                    </label>
                                    <flux:textarea wire:model="close_reason" rows="2" placeholder="What helped win this opportunity? (optional)" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if($activeTab === 'activities')
                <!-- Activity Stream -->
                <div class="p-4 sm:p-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Activity Stream</h3>
                        </div>

                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($activities as $activity)
                                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <div class="flex space-x-3">
                                        <div class="flex-shrink-0 hidden sm:block">
                                            <div class="w-8 h-8 rounded-full bg-{{ $activity->type_color }}-100 dark:bg-{{ $activity->type_color }}-900/30 flex items-center justify-center">
                                                @switch($activity->type)
                                                    @case('call')
                                                        <flux:icon.phone class="w-4 h-4 text-{{ $activity->type_color }}-600 dark:text-{{ $activity->type_color }}-400" />
                                                        @break
                                                    @case('email')
                                                        <flux:icon.envelope class="w-4 h-4 text-{{ $activity->type_color }}-600 dark:text-{{ $activity->type_color }}-400" />
                                                        @break
                                                    @case('meeting')
                                                        <flux:icon.calendar class="w-4 h-4 text-{{ $activity->type_color }}-600 dark:text-{{ $activity->type_color }}-400" />
                                                        @break
                                                    @case('note')
                                                        <flux:icon.document-text class="w-4 h-4 text-{{ $activity->type_color }}-600 dark:text-{{ $activity->type_color }}-400" />
                                                        @break
                                                    @default
                                                        <flux:icon.clipboard-document-list class="w-4 h-4 text-{{ $activity->type_color }}-600 dark:text-{{ $activity->type_color }}-400" />
                                                @endswitch
                                            </div>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-1 sm:space-y-0">
                                                <div class="flex items-center space-x-2">
                                                    <div class="sm:hidden w-4 h-4 rounded-full bg-{{ $activity->type_color }}-100 dark:bg-{{ $activity->type_color }}-900/30 flex items-center justify-center">
                                                        @switch($activity->type)
                                                            @case('call')
                                                                <flux:icon.phone class="w-2.5 h-2.5 text-{{ $activity->type_color }}-600 dark:text-{{ $activity->type_color }}-400" />
                                                                @break
                                                            @case('email')
                                                                <flux:icon.envelope class="w-2.5 h-2.5 text-{{ $activity->type_color }}-600 dark:text-{{ $activity->type_color }}-400" />
                                                                @break
                                                            @case('meeting')
                                                                <flux:icon.calendar class="w-2.5 h-2.5 text-{{ $activity->type_color }}-600 dark:text-{{ $activity->type_color }}-400" />
                                                                @break
                                                            @case('note')
                                                                <flux:icon.document-text class="w-2.5 h-2.5 text-{{ $activity->type_color }}-600 dark:text-{{ $activity->type_color }}-400" />
                                                                @break
                                                            @default
                                                                <flux:icon.clipboard-document-list class="w-2.5 h-2.5 text-{{ $activity->type_color }}-600 dark:text-{{ $activity->type_color }}-400" />
                                                        @endswitch
                                                    </div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $activity->subject }}
                                                    </p>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    @if($activity->status)
                                                        <flux:badge :variant="$activity->status_color" size="sm">
                                                            {{ ucfirst($activity->status) }}
                                                        </flux:badge>
                                                    @endif
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $activity->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>

                                            @if($activity->description)
                                                <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                    @if($activity->subject === 'Opportunity Updated' && str_contains($activity->description, 'Updated the following:'))
                                                        @php
                                                            $lines = explode("\n", $activity->description);
                                                            $header = array_shift($lines); // Remove "Updated the following:"
                                                        @endphp
                                                        <div class="mt-2 bg-gray-50 dark:bg-gray-800 rounded-md p-3">
                                                            <div class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Changes made:</div>
                                                            <div class="space-y-1.5">
                                                                @foreach($lines as $line)
                                                                    @if(trim($line))
                                                                        @php
                                                                            $parts = explode(':', trim($line), 2);
                                                                            $field = $parts[0] ?? '';
                                                                            $change = $parts[1] ?? '';
                                                                        @endphp
                                                                        <div class="flex flex-col space-y-0.5">
                                                                            <div class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $field }}</div>
                                                                            <div class="text-xs text-gray-800 dark:text-gray-200 pl-3 border-l-2 border-blue-300 dark:border-blue-600">
                                                                                {{ $change }}
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        {!! nl2br(e($activity->description)) !!}
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                                <span>{{ $activity->user->name }}</span>
                                                @if($activity->scheduled_at && $activity->status === 'pending')
                                                    <span>•</span>
                                                    <span>Scheduled for {{ $activity->scheduled_at->format('M d, Y g:i A') }}</span>
                                                @endif
                                                @if($activity->completed_at)
                                                    <span>•</span>
                                                    <span>Completed {{ $activity->completed_at->format('M d, Y g:i A') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <flux:icon.clipboard-document-list class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-4" />
                                    <p class="text-gray-500 dark:text-gray-400">No activities yet</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500">Activities will appear here as they're added</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Sidebar -->
        <div class="w-full lg:w-80 bg-white dark:bg-gray-800 border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-gray-700 overflow-y-auto">
            <div class="p-4 sm:p-6">
                                <!-- Quick Actions -->
                <div class="space-y-3">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white uppercase tracking-wide">Quick Actions</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-3">
                        <flux:button wire:click="openTaskModal" variant="outline" class="w-full justify-start">
                            <flux:icon.clipboard-document-list class="w-4 h-4 mr-3" />
                            Add Task
                        </flux:button>

                        <flux:button wire:click="openNoteModal" variant="outline" class="w-full justify-start">
                            <flux:icon.document-text class="w-4 h-4 mr-3" />
                            Add Note
                        </flux:button>

                        <flux:button wire:click="openActivityModal" variant="outline" class="w-full justify-start">
                            <flux:icon.calendar class="w-4 h-4 mr-3" />
                            Schedule Activity
                        </flux:button>
                    </div>
                </div>

                <!-- Deal Information -->
                <div class="mt-8">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white uppercase tracking-wide mb-4">Deal Information</h3>

                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deal name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $opportunity->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deal stage</dt>
                            <dd class="mt-1">
                                @if($opportunity->stage === 'prospecting')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                        {{ ucfirst($opportunity->stage) }}
                                    </span>
                                @elseif($opportunity->stage === 'proposal')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                        {{ ucfirst($opportunity->stage) }}
                                    </span>
                                @elseif($opportunity->stage === 'negotiation')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                        {{ ucfirst($opportunity->stage) }}
                                    </span>
                                @elseif($opportunity->stage === 'won')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        {{ ucfirst($opportunity->stage) }}
                                    </span>
                                @elseif($opportunity->stage === 'lost')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                        {{ ucfirst($opportunity->stage) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                        {{ ucfirst($opportunity->stage) }}
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deal value</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">₹{{ number_format($opportunity->value, 2) }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deal owner</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $opportunity->creator->name }}</dd>
                        </div>

                        @if($opportunity->expected_close_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Expected close</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $opportunity->expected_close_date->format('M d, Y') }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Associated People -->
                <div class="mt-8">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white uppercase tracking-wide mb-4">Associated People</h3>

                    @if($opportunity->lead->customer)
                        <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                                <span class="text-xs font-medium text-white">
                                    {{ substr($opportunity->lead->customer->name, 0, 2) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $opportunity->lead->customer->name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Customer</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

        <!-- Task Modal -->
    @if($showTaskModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end sm:items-center justify-center min-h-screen px-4 pt-4 pb-4 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeTaskModal"></div>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-t-lg sm:rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg w-full sm:w-full">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add Task</h3>
                    </div>

                    <form wire:submit.prevent="saveTask" class="p-4 sm:p-6 space-y-4">
                        <div>
                            <flux:field>
                                <flux:label>Task Type</flux:label>
                                <flux:select wire:model="taskType">
                                    <option value="task">Task</option>
                                    <option value="call">Call</option>
                                    <option value="email">Email</option>
                                    <option value="meeting">Meeting</option>
                                </flux:select>
                                <flux:error name="taskType" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Subject</flux:label>
                                <flux:input wire:model="taskSubject" placeholder="Enter task subject" />
                                <flux:error name="taskSubject" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Description</flux:label>
                                <flux:textarea wire:model="taskDescription" rows="3" placeholder="Enter task description" />
                                <flux:error name="taskDescription" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Scheduled Date & Time</flux:label>
                                <flux:input wire:model="taskScheduledAt" type="datetime-local" />
                                <flux:error name="taskScheduledAt" />
                            </flux:field>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-4">
                            <flux:button type="button" wire:click="closeTaskModal" variant="ghost" class="w-full sm:w-auto">
                                Cancel
                            </flux:button>
                            <flux:button type="submit" variant="filled" class="w-full sm:w-auto">
                                Create Task
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

        <!-- Note Modal -->
    @if($showNoteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end sm:items-center justify-center min-h-screen px-4 pt-4 pb-4 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeNoteModal"></div>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-t-lg sm:rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg w-full sm:w-full">
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add Note</h3>
                    </div>

                    <form wire:submit.prevent="saveNote" class="p-4 sm:p-6 space-y-4">
                        <div>
                            <flux:field>
                                <flux:label>Subject</flux:label>
                                <flux:input wire:model="noteSubject" placeholder="Enter note subject" />
                                <flux:error name="noteSubject" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Note</flux:label>
                                <flux:textarea wire:model="noteDescription" rows="4" placeholder="Enter your note here..." />
                                <flux:error name="noteDescription" />
                            </flux:field>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-4">
                            <flux:button type="button" wire:click="closeNoteModal" variant="ghost" class="w-full sm:w-auto">
                                Cancel
                            </flux:button>
                            <flux:button type="submit" variant="filled" class="w-full sm:w-auto">
                                Add Note
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

        <!-- Activity Modal -->
    @if($showActivityModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end sm:items-center justify-center min-h-screen px-4 pt-4 pb-4 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeActivityModal"></div>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-t-lg sm:rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg w-full sm:w-full">
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Schedule Activity</h3>
                    </div>

                    <form wire:submit.prevent="saveActivity" class="p-4 sm:p-6 space-y-4">
                        <div>
                            <flux:field>
                                <flux:label>Activity Type</flux:label>
                                <flux:select wire:model="activityType">
                                    <option value="call">Call</option>
                                    <option value="email">Email</option>
                                    <option value="meeting">Meeting</option>
                                    <option value="task">Task</option>
                                </flux:select>
                                <flux:error name="activityType" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Subject</flux:label>
                                <flux:input wire:model="activitySubject" placeholder="Enter activity subject" />
                                <flux:error name="activitySubject" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Description</flux:label>
                                <flux:textarea wire:model="activityDescription" rows="3" placeholder="Enter activity description" />
                                <flux:error name="activityDescription" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Scheduled Date & Time</flux:label>
                                <flux:input wire:model="activityScheduledAt" type="datetime-local" />
                                <flux:error name="activityScheduledAt" />
                            </flux:field>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-4">
                            <flux:button type="button" wire:click="closeActivityModal" variant="ghost" class="w-full sm:w-auto">
                                Cancel
                            </flux:button>
                            <flux:button type="submit" variant="filled" class="w-full sm:w-auto">
                                Schedule Activity
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
