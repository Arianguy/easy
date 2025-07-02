<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">CRM Dashboard</h1>
            <div class="flex space-x-4">
                @if(auth()->user()->canManageAllBranches())
                    <select wire:model="selectedBranch" class="rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                        <option value="">All Branches</option>
                        @foreach(\App\Models\Branch::all() as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                @endif
                <select wire:model="selectedPeriod" class="rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                </select>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Customers -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-md bg-blue-500 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Customers</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ number_format($stats['total_customers']) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="text-green-600 dark:text-green-400 font-medium">+{{ $stats['new_customers'] }}</span> new this period
                    </div>
                </div>
            </div>

            <!-- Leads -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-md bg-yellow-500 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Leads</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ number_format($stats['active_leads']) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="text-green-600 dark:text-green-400 font-medium">{{ $stats['conversion_rate'] }}%</span> conversion rate
                    </div>
                </div>
            </div>

            <!-- Opportunities -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-md bg-purple-500 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pipeline Value</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">₹{{ number_format($stats['pipeline_value']) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="text-blue-600 dark:text-blue-400 font-medium">₹{{ number_format($stats['weighted_pipeline']) }}</span> weighted
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-md bg-green-500 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Revenue</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">₹{{ number_format($stats['total_revenue']) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="text-green-600 dark:text-green-400 font-medium">₹{{ number_format($stats['avg_deal_size']) }}</span> avg deal
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Activities -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Recent Activities</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recentActivities as $activity)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-{{ $activity->type_color }}-100 dark:bg-{{ $activity->type_color }}-900/30 flex items-center justify-center">
                                        <div class="h-2 w-2 bg-{{ $activity->type_color }}-600 dark:bg-{{ $activity->type_color }}-400 rounded-full"></div>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $activity->subject }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $activity->user->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No recent activities</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Overdue Leads -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Overdue Leads</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($overdueLeads as $lead)
                            <div class="flex items-start justify-between">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $lead->title }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $lead->customer->name ?? 'No customer' }}</p>
                                    <p class="text-xs text-red-600 dark:text-red-400">Due: {{ $lead->follow_up_date->format('M j, Y') }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $lead->priority_color }}-100 dark:bg-{{ $lead->priority_color }}-900/30 text-{{ $lead->priority_color }}-800 dark:text-{{ $lead->priority_color }}-300">
                                        {{ ucfirst($lead->priority) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No overdue leads</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Performance -->
        @if($topPerformers->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Top Performers</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($topPerformers as $performer)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ substr($performer->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $performer->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $performer->total_leads }} leads</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $performer->conversion_rate }}%</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">conversion</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
