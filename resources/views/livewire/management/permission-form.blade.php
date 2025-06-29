<div>
    <flux:header class="space-y-2">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">
                    {{ $isEditing ? 'Edit Permission' : 'Create Permission' }}
                </flux:heading>
                <flux:subheading>
                    {{ $isEditing ? 'Update permission details and role assignments' : 'Add a new system permission' }}
                </flux:subheading>
            </div>

            <flux:button variant="ghost" :href="route('permissions.index')" wire:navigate>
                Back to Permissions
            </flux:button>
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

    <form wire:submit="save" class="space-y-6">
        <!-- Permission Details Card -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Permission Details</h3>

            <div class="space-y-4">
                <!-- Permission Name -->
                <div>
                    <flux:field>
                        <flux:label for="name">Permission Name</flux:label>
                        <flux:input
                            id="name"
                            wire:model="name"
                            placeholder="e.g., view users, create reports, manage settings"
                            required
                        />
                        <flux:error name="name" />
                    </flux:field>
                    <p class="mt-1 text-sm text-gray-500">
                        Use a descriptive name that clearly indicates what this permission allows (e.g., "view customers", "delete leads").
                    </p>
                </div>
            </div>
        </div>

        <!-- Role Assignment Card -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Role Assignment</h3>
            <p class="text-sm text-gray-600 mb-4">
                Select which roles should have this permission. You can assign the same permission to multiple roles.
            </p>

            <div class="space-y-3">
                @foreach($roles as $role)
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="role_{{ $role->id }}"
                            wire:model="selectedRoles"
                            value="{{ $role->id }}"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="role_{{ $role->id }}" class="ml-3 flex items-center">
                            <span class="text-sm font-medium text-gray-900">{{ $role->name }}</span>
                            <span class="ml-2 text-xs text-gray-500">
                                ({{ $role->permissions->count() }} permissions)
                            </span>
                        </label>
                    </div>
                @endforeach

                @if($roles->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM9 9a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No roles available</h3>
                        <p class="mt-1 text-sm text-gray-500">Create some roles first to assign permissions to them.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
            <flux:button variant="ghost" wire:click="cancel">
                Cancel
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ $isEditing ? 'Update Permission' : 'Create Permission' }}
            </flux:button>
        </div>
    </form>
</div>
