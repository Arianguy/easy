<div x-data="{
    open: <?php if ((object) ('showDropdown') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showDropdown'->value()); ?>')<?php echo e('showDropdown'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showDropdown'); ?>')<?php endif; ?>,
    buttonRect: null,
    updatePosition() {
        if (this.open) {
            this.$nextTick(() => {
                const button = this.$refs.button;
                this.buttonRect = button.getBoundingClientRect();
            });
        }
    }
}"
x-init="$watch('open', () => updatePosition())"
wire:poll.60s="refreshNotifications"
class="relative">
    <!-- Notification Bell Button -->
    <button
        x-ref="button"
        @click="open = !open"
        class="relative p-1.5 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 rounded-lg transition-colors"
    >
        <!-- Bell Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        <!-- Red Dot with Count -->
        <!--[if BLOCK]><![endif]--><?php if($unreadCount > 0): ?>
            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full min-w-[1rem] h-4 animate-pulse">
                <?php echo e($unreadCount > 99 ? '99+' : $unreadCount); ?>

            </span>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </button>

    <!-- Teleported Dropdown Menu - Always at body level to avoid z-index issues -->
        <template x-teleport="body">
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        @click.away="open = false"
        class="fixed w-72 lg:w-80 bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700"
        style="z-index: 99999; display: none;"
        x-data="{
            setPosition() {
                // Always position from bottom, regardless of events
                const sidebar = document.querySelector('[data-flux-sidebar]') || document.querySelector('.lg\\:block');
                const isMobile = window.innerWidth < 1024;

                if (isMobile) {
                    // Mobile positioning - always from bottom
                    $el.style.bottom = '1rem';
                    $el.style.right = '1rem';
                    $el.style.left = 'auto';
                    $el.style.top = 'auto';
                    $el.style.transform = 'none';
                } else {
                    // Desktop positioning - always from bottom right of sidebar
                    const sidebarWidth = sidebar ? sidebar.offsetWidth : 256;
                    $el.style.left = (sidebarWidth + 16) + 'px';
                    $el.style.bottom = '1rem';
                    $el.style.top = 'auto';
                    $el.style.transform = 'none';
                    $el.style.right = 'auto';
                }
            }
        }"
        x-init="
            $watch('open', (value) => {
                if (value) {
                    $nextTick(() => this.setPosition());
                }
            });
        "
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications</h3>
                <!--[if BLOCK]><![endif]--><?php if($unreadCount > 0): ?>
                    <button
                        wire:click="markAllAsRead"
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                    >
                        Mark all as read
                    </button>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-80 lg:max-h-96 overflow-y-auto">
            <!--[if BLOCK]><![endif]--><?php if(count($notifications) > 0): ?>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div
                        class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors <?php echo e($notification['read_at'] ? 'opacity-75' : 'bg-blue-50 dark:bg-blue-900/20'); ?>"
                        wire:click="markAsRead('<?php echo e($notification['id']); ?>')"
                    >
                        <div class="flex items-start space-x-3">
                            <!-- Notification Icon -->
                            <div class="flex-shrink-0 mt-1">
                                <!--[if BLOCK]><![endif]--><?php if(str_contains($notification['type'], 'FollowUpReminder')): ?>
                                    <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                                <?php elseif(str_contains($notification['type'], 'ActivityReminder')): ?>
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <?php else: ?>
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <!-- Notification Content -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?php echo e($notification['data']['message'] ?? 'New notification'); ?>

                                </p>

                                <!--[if BLOCK]><![endif]--><?php if(isset($notification['data']['lead_title'])): ?>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        Lead: <?php echo e($notification['data']['lead_title']); ?>

                                    </p>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <!--[if BLOCK]><![endif]--><?php if(isset($notification['data']['activity_subject'])): ?>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        Activity: <?php echo e($notification['data']['activity_subject']); ?>

                                    </p>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <?php echo e($notification['time_ago']); ?>

                                </p>
                            </div>

                            <!-- Unread Indicator -->
                            <!--[if BLOCK]><![endif]--><?php if(!$notification['read_at']): ?>
                                <div class="flex-shrink-0">
                                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            <?php else: ?>
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No notifications yet</p>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <!-- Footer -->
        <!--[if BLOCK]><![endif]--><?php if(count($notifications) > 0): ?>
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                <a
                    href="#"
                    class="block text-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                >
                    View all notifications
                </a>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
    </template>
</div>
<?php /**PATH C:\Users\SAVIO\Desktop\easy\resources\views/livewire/notification-bell.blade.php ENDPATH**/ ?>