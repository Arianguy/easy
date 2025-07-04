<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FollowUpReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Lead $lead,
        public string $type = 'upcoming' // 'upcoming', 'overdue', 'today'
    ) {}

    public function via($notifiable): array
    {
        $channels = ['database']; // Always send to database

        // Only send email if user wants email reminders and lead reminders
        if ($notifiable->wantsEmailReminders() && $notifiable->wantsLeadReminders()) {
            // Check specific reminder type preferences
            if (($this->type === 'overdue' && $notifiable->wantsOverdueNotifications()) ||
                ($this->type === 'upcoming' && $notifiable->wantsUpcomingNotifications()) ||
                $this->type === 'today'
            ) {
                $channels[] = 'mail';
            }
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $subject = match ($this->type) {
            'overdue' => 'Overdue Follow-up Required',
            'today' => 'Follow-up Due Today',
            default => 'Upcoming Follow-up Reminder'
        };

        $greeting = match ($this->type) {
            'overdue' => 'You have an overdue follow-up!',
            'today' => 'You have a follow-up due today!',
            default => 'You have an upcoming follow-up!'
        };

        return (new MailMessage)
            ->subject($subject . ' - ' . $this->lead->title)
            ->greeting($greeting)
            ->line('**Lead:** ' . $this->lead->title)
            ->line('**Customer:** ' . ($this->lead->customer->name ?? 'N/A'))
            ->line('**Priority:** ' . ucfirst($this->lead->priority))
            ->line('**Follow-up Date:** ' . $this->lead->follow_up_date->format('M j, Y'))
            ->when($this->type === 'overdue', function ($message) {
                return $message->line('⚠️ This follow-up is **overdue** by ' .
                    $this->lead->follow_up_date->diffForHumans() . '.');
            })
            ->line('**Description:** ' . ($this->lead->description ?? 'No description provided'))
            ->action('View Lead', url('/leads/' . $this->lead->id))
            ->line('Please take action on this follow-up as soon as possible.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'follow_up_reminder',
            'lead_id' => $this->lead->id,
            'lead_title' => $this->lead->title,
            'customer_name' => $this->lead->customer->name ?? null,
            'follow_up_date' => $this->lead->follow_up_date->toDateString(),
            'priority' => $this->lead->priority,
            'reminder_type' => $this->type,
            'message' => $this->getNotificationMessage(),
        ];
    }

    private function getNotificationMessage(): string
    {
        return match ($this->type) {
            'overdue' => 'Follow-up for "' . $this->lead->title . '" is overdue',
            'today' => 'Follow-up for "' . $this->lead->title . '" is due today',
            default => 'Follow-up for "' . $this->lead->title . '" is coming up'
        };
    }
}
