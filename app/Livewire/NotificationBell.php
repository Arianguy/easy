<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    public $unreadCount = 0;
    public $notifications = [];
    public $showDropdown = false;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::user();

        if (!$user) {
            $this->unreadCount = 0;
            $this->notifications = [];
            return;
        }

        try {
            $this->unreadCount = DB::table('notifications')
                ->where('notifiable_type', 'App\\Models\\User')
                ->where('notifiable_id', $user->id)
                ->whereNull('read_at')
                ->count();

            $notificationsData = DB::table('notifications')
                ->where('notifiable_type', 'App\\Models\\User')
                ->where('notifiable_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $this->notifications = [];
            foreach ($notificationsData as $notification) {
                if ($notification) {
                    $data = json_decode($notification->data, true) ?? [];
                    $this->notifications[] = [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'data' => $data,
                        'read_at' => $notification->read_at,
                        'created_at' => $notification->created_at,
                        'time_ago' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->unreadCount = 0;
            $this->notifications = [];
        }
    }

    #[On('notification-sent')]
    public function refreshNotifications()
    {
        $this->loadNotifications();
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
        if ($this->showDropdown) {
            $this->loadNotifications();
        }
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        if (!$user || !$notificationId) return;

        try {
            DB::table('notifications')
                ->where('id', $notificationId)
                ->where('notifiable_type', 'App\\Models\\User')
                ->where('notifiable_id', $user->id)
                ->update(['read_at' => now()]);
            $this->loadNotifications();
        } catch (\Exception $e) {
            // Handle silently
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        if (!$user) return;

        try {
            DB::table('notifications')
                ->where('notifiable_type', 'App\\Models\\User')
                ->where('notifiable_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            $this->loadNotifications();
        } catch (\Exception $e) {
            // Handle silently
        }
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
