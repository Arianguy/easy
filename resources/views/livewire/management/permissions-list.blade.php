<div>
        <flux:header class="space-y-2">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Permissions</flux:heading>
                <flux:subheading>Manage system permissions organized by category ({{ $totalPermissions }} total)</flux:subheading>
            </div>

            @can('create permissions')
                <flux:button icon="plus" :href="route('permissions.create')" wire:navigate>
                    Add Permission
                </flux:button>
            @endcan
        </div>
    </flux:header>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

        <!-- Search -->
    <div class="mb-6">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Search permissions across all categories..."
            icon="magnifying-glass"
            class="max-w-md"
        />
    </div>

            <!-- Permission Categories Grid -->
    @if($groupedPermissions->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($groupedPermissions as $group)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <!-- Category Header -->
                    <div class="bg-gradient-to-r from-{{ $group['color'] }}-50 to-{{ $group['color'] }}-100 border-b border-{{ $group['color'] }}-200 px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-{{ $group['color'] }}-600 rounded-lg flex items-center justify-center">
                                    <flux:icon name="{{ $group['icon'] }}" class="w-5 h-5 text-white" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $group['name'] }}</h3>
                                <p class="text-sm text-{{ $group['color'] }}-700">{{ $group['permissions']->count() }} permissions</p>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions List -->
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($group['permissions'] as $permission)
                                <div class="border border-gray-100 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 mb-2">{{ $permission->name }}</h4>

                                            <!-- Assigned Roles -->
                                            <div class="mb-3">
                                                <div class="flex flex-wrap gap-1">
                                                    @forelse($permission->roles as $role)
                                                        <flux:badge size="sm" color="{{ $group['color'] }}">{{ $role->name }}</flux:badge>
                                                    @empty
                                                        <span class="text-xs text-gray-500">No roles assigned</span>
                                                    @endforelse
                                                </div>
                                            </div>

                                            <!-- Created Date -->
                                            <p class="text-xs text-gray-500">
                                                Created {{ $permission->created_at->format('M d, Y') }}
                                            </p>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex items-center gap-1 ml-4">
                                            @can('edit permissions')
                                                <flux:button
                                                    size="sm"
                                                    variant="ghost"
                                                    icon="pencil"
                                                    :href="route('permissions.edit', $permission)"
                                                    wire:navigate
                                                    class="text-{{ $group['color'] }}-600 hover:text-{{ $group['color'] }}-700"
                                                >
                                                </flux:button>
                                            @endcan

                                            @can('delete permissions')
                                                <flux:button
                                                    size="sm"
                                                    variant="ghost"
                                                    icon="trash"
                                                    wire:click="deletePermission({{ $permission->id }})"
                                                    wire:confirm="Are you sure you want to delete this permission?"
                                                    class="text-red-600 hover:text-red-700"
                                                >
                                                </flux:button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">
                @if($search)
                    No permissions found
                @else
                    No permissions available
                @endif
            </h3>
            <p class="mt-2 text-sm text-gray-500">
                @if($search)
                    No permissions match your search criteria. Try adjusting your search terms.
                @else
                    Get started by creating your first permission to manage system access.
                @endif
            </p>
            @if(!$search)
                @can('create permissions')
                    <div class="mt-6">
                        <flux:button icon="plus" :href="route('permissions.create')" wire:navigate>
                            Create First Permission
                        </flux:button>
                    </div>
                @endcan
            @endif
        </div>
    @endif
</div>
