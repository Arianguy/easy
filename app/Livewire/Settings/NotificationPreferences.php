<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationPreferences extends Component
{
    public $email_reminders = true;
    public $lead_reminders = true;
    public $activity_reminders = true;
    public $overdue_notifications = true;
    public $upcoming_notifications = true;

    public function mount()
    {
        $user = Auth::user();
        $this->email_reminders = $user->email_reminders ?? true;
        $this->lead_reminders = $user->lead_reminders ?? true;
        $this->activity_reminders = $user->activity_reminders ?? true;
        $this->overdue_notifications = $user->overdue_notifications ?? true;
        $this->upcoming_notifications = $user->upcoming_notifications ?? true;
    }

    public function save()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update([
            'email_reminders' => $this->email_reminders,
            'lead_reminders' => $this->lead_reminders,
            'activity_reminders' => $this->activity_reminders,
            'overdue_notifications' => $this->overdue_notifications,
            'upcoming_notifications' => $this->upcoming_notifications,
        ]);

        session()->flash('message', 'Notification preferences updated successfully.');

        $this->dispatch('preferences-updated');
    }

    public function render()
    {
        return view('livewire.settings.notification-preferences');
    }
}
