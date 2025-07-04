<div>
    <x-settings.layout
        :heading="__('Notification Preferences')"
        :subheading="__('Manage your follow-up reminder and notification settings')"
    >
        <form wire:submit.prevent="save" class="space-y-6">

            <!-- Email Notifications -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Email Notifications
                </h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Enable Email Reminders
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Receive follow-up reminders via email
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="email_reminders" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reminder Types -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Reminder Types
                </h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Lead Follow-up Reminders
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Get notified about lead follow-up dates
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="lead_reminders" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Activity Reminders
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Get notified about scheduled activities
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="activity_reminders" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timing Preferences -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Timing Preferences
                </h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Overdue Notifications
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Get notified about overdue follow-ups and activities
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="overdue_notifications" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Upcoming Notifications
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Get notified about upcoming follow-ups and activities
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="upcoming_notifications" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Box -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            Notification Schedule
                        </h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Overdue reminders: Every 2 hours during business hours</li>
                                <li>Daily reminders: Every hour during business hours</li>
                                <li>Upcoming reminders: Once daily at 8:00 AM</li>
                                <li>All notifications are sent only on weekdays</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Save Preferences
                </button>
            </div>

            <!-- Success Message -->
            @if (session()->has('message'))
                <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ session('message') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </form>
    </x-settings.layout>
</div>
