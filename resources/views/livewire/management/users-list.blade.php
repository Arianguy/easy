<flux:main>
    <flux:header>
        <flux:heading size="lg">{{ __('User Management') }}</flux:heading>

        <flux:spacer />

        <flux:button :href="route('users.create')" wire:navigate icon="plus">
            Add User
        </flux:button>
    </flux:header>

    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="flex flex-col sm:flex-row gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search users..."
                icon="magnifying-glass"
                class="flex-1"
            />

            <div class="w-full sm:w-48">
                <select
                    wire:model.live="roleFilter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Roles</option>
                    <option value="Area Manager">Area Manager</option>
                    <option value="Sales Manager">Sales Manager</option>
                    <option value="Sales Executive">Sales Executive</option>
                </select>
            </div>
        </div>

        <!-- Role Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:badge color="purple" size="lg">AM</flux:badge>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Area Managers</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->roleStats['Area Manager'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:badge color="blue" size="lg">SM</flux:badge>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Sales Managers</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->roleStats['Sales Manager'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:badge color="green" size="lg">SE</flux:badge>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Sales Executives</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->roleStats['Sales Executive'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Branch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Login</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->roles->isNotEmpty())
                                        @foreach($user->roles as $role)
                                            <flux:badge
                                                :color="$role->name === 'Area Manager' ? 'purple' : ($role->name === 'Sales Manager' ? 'blue' : 'green')"
                                                size="sm"
                                                class="mr-1"
                                            >
                                                {{ $role->name }}
                                            </flux:badge>
                                        @endforeach
                                    @else
                                        <flux:badge color="gray" size="sm">No Role</flux:badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $user->branch ? $user->branch->name : 'No Branch' }}
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge
                                        :color="$user->is_active ? 'green' : 'red'"
                                        size="sm"
                                    >
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <flux:button
                                            :href="route('users.edit', $user)"
                                            variant="ghost"
                                            size="sm"
                                            wire:navigate
                                        >
                                            Edit
                                        </flux:button>
                                        @if($user->id !== auth()->id())
                                            <flux:button
                                                wire:click="toggleStatus({{ $user->id }})"
                                                variant="ghost"
                                                size="sm"
                                                :color="$user->is_active ? 'red' : 'green'"
                                            >
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </flux:button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    @if($search || $roleFilter)
                                        No users found matching your criteria
                                    @else
                                        No users found. <flux:link :href="route('users.create')" wire:navigate class="text-blue-600 hover:underline">Create your first user</flux:link>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        @endif

        <!-- Role Information Panel -->
        <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <flux:heading size="sm" class="text-blue-900 dark:text-blue-100 mb-3">Role Permissions</flux:heading>
            <div class="text-sm text-blue-800 dark:text-blue-200 space-y-2">
                <div><strong>Area Manager:</strong> Full access to all branches, users, and CRM data</div>
                <div><strong>Sales Manager:</strong> Manage team members and customers within their branch</div>
                <div><strong>Sales Executive:</strong> Manage customers, leads, and opportunities within their branch</div>
            </div>
        </div>
    </div>
</flux:main>
