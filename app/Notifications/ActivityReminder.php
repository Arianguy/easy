<?php

namespace App\Notifications;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActivityReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Activity $activity,
        public string $type = 'upcoming' // 'upcoming', 'overdue', 'today'
    ) {}

    public function via($notifiable): array
    {
        $channels = ['database']; // Always send to database

        // Only send email if user wants email reminders and activity reminders
        if ($notifiable->wantsEmailReminders() && $notifiable->wantsActivityReminders()) {
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
            'overdue' => 'Overdue Activity',
            'today' => 'Activity Due Today',
            default => 'Upcoming Activity Reminder'
        };

        $greeting = match ($this->type) {
            'overdue' => 'You have an overdue activity!',
            'today' => 'You have an activity due today!',
            default => 'You have an upcoming activity!'
        };

        $relatedInfo = '';
        if ($this->activity->related) {
            $relatedType = class_basename($this->activity->related_type);
            $relatedName = $this->activity->related->name ?? $this->activity->related->title ?? 'Untitled';
            $relatedInfo = "**Related {$relatedType}:** {$relatedName}";
        }

        return (new MailMessage)
            ->subject($subject . ' - ' . $this->activity->subject)
            ->greeting($greeting)
            ->line('**Activity:** ' . $this->activity->subject)
            ->line('**Type:** ' . ucfirst($this->activity->type))
            ->line('**Scheduled:** ' . $this->activity->scheduled_at->format('M j, Y \a\t g:i A'))
            ->when($relatedInfo, fn($message) => $message->line($relatedInfo))
            ->when($this->type === 'overdue', function ($message) {
                return $message->line('⚠️ This activity is **overdue** by ' .
                    $this->activity->scheduled_at->diffForHumans() . '.');
            })
            ->when($this->activity->description, fn($message) =>
            $message->line('**Description:** ' . $this->activity->description))
            ->action('View Activity', url('/activities/' . $this->activity->id))
            ->line('Please complete this activity as soon as possible.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'activity_reminder',
            'activity_id' => $this->activity->id,
            'activity_subject' => $this->activity->subject,
            'activity_type' => $this->activity->type,
            'scheduled_at' => $this->activity->scheduled_at->toDateTimeString(),
            'related_type' => $this->activity->related_type,
            'related_id' => $this->activity->related_id,
            'reminder_type' => $this->type,
            'message' => $this->getNotificationMessage(),
        ];
    }

    private function getNotificationMessage(): string
    {
        return match ($this->type) {
            'overdue' => 'Activity "' . $this->activity->subject . '" is overdue',
            'today' => 'Activity "' . $this->activity->subject . '" is due today',
            default => 'Activity "' . $this->activity->subject . '" is coming up'
        };
    }
}
