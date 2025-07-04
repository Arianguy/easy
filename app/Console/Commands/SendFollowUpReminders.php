<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\Activity;
use App\Notifications\FollowUpReminder;
use App\Notifications\ActivityReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendFollowUpReminders extends Command
{
    protected $signature = 'reminders:send
                            {--type=all : Type of reminders to send (all, leads, activities)}
                            {--days=1 : Days ahead to check for upcoming reminders}
                            {--dry-run : Show what would be sent without actually sending}';

    protected $description = 'Send follow-up reminders for leads and activities';

    public function handle()
    {
        $type = $this->option('type');
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("🔔 Starting reminder check...");
        $this->info("Type: {$type} | Days ahead: {$days} | Dry run: " . ($dryRun ? 'Yes' : 'No'));

        $totalSent = 0;

        // Send lead follow-up reminders
        if ($type === 'all' || $type === 'leads') {
            $totalSent += $this->sendLeadReminders($days, $dryRun);
        }

        // Send activity reminders
        if ($type === 'all' || $type === 'activities') {
            $totalSent += $this->sendActivityReminders($days, $dryRun);
        }

        $this->info("✅ Reminder check completed. Total notifications " .
            ($dryRun ? 'would be sent' : 'sent') . ": {$totalSent}");

        Log::info('Follow-up reminders processed', [
            'type' => $type,
            'days_ahead' => $days,
            'dry_run' => $dryRun,
            'total_sent' => $totalSent
        ]);
    }

    private function sendLeadReminders(int $days, bool $dryRun): int
    {
        $this->info("📋 Checking lead follow-ups...");
        $sent = 0;

        // Overdue leads
        $overdueLeads = Lead::overdue()
            ->with(['customer', 'assignedUser'])
            ->whereHas('assignedUser')
            ->get();

        foreach ($overdueLeads as $lead) {
            if ($dryRun) {
                $this->line("  [DRY RUN] Would send OVERDUE reminder for: {$lead->title} (Due: {$lead->follow_up_date->format('M j, Y')})");
            } else {
                $lead->assignedUser->notify(new FollowUpReminder($lead, 'overdue'));
                $this->line("  📧 Sent OVERDUE reminder for: {$lead->title}");
            }
            $sent++;
        }

        // Due today
        $todayLeads = Lead::dueToday()
            ->with(['customer', 'assignedUser'])
            ->whereHas('assignedUser')
            ->get();

        foreach ($todayLeads as $lead) {
            if ($dryRun) {
                $this->line("  [DRY RUN] Would send TODAY reminder for: {$lead->title}");
            } else {
                $lead->assignedUser->notify(new FollowUpReminder($lead, 'today'));
                $this->line("  📧 Sent TODAY reminder for: {$lead->title}");
            }
            $sent++;
        }

        // Upcoming leads
        $upcomingLeads = Lead::upcoming($days)
            ->with(['customer', 'assignedUser'])
            ->whereHas('assignedUser')
            ->where('follow_up_date', '>', today()) // Exclude today (already handled above)
            ->get();

        foreach ($upcomingLeads as $lead) {
            if ($dryRun) {
                $this->line("  [DRY RUN] Would send UPCOMING reminder for: {$lead->title} (Due: {$lead->follow_up_date->format('M j, Y')})");
            } else {
                $lead->assignedUser->notify(new FollowUpReminder($lead, 'upcoming'));
                $this->line("  📧 Sent UPCOMING reminder for: {$lead->title}");
            }
            $sent++;
        }

        $this->info("  📋 Lead reminders: {$sent}");
        return $sent;
    }

    private function sendActivityReminders(int $days, bool $dryRun): int
    {
        $this->info("📅 Checking activity schedules...");
        $sent = 0;

        // Overdue activities
        $overdueActivities = Activity::overdue()
            ->with(['user', 'related'])
            ->whereHas('user')
            ->get();

        foreach ($overdueActivities as $activity) {
            if ($dryRun) {
                $this->line("  [DRY RUN] Would send OVERDUE activity reminder for: {$activity->subject} (Due: {$activity->scheduled_at->format('M j, Y g:i A')})");
            } else {
                $activity->user->notify(new ActivityReminder($activity, 'overdue'));
                $this->line("  📧 Sent OVERDUE activity reminder for: {$activity->subject}");
            }
            $sent++;
        }

        // Due today
        $todayActivities = Activity::dueToday()
            ->with(['user', 'related'])
            ->whereHas('user')
            ->get();

        foreach ($todayActivities as $activity) {
            if ($dryRun) {
                $this->line("  [DRY RUN] Would send TODAY activity reminder for: {$activity->subject}");
            } else {
                $activity->user->notify(new ActivityReminder($activity, 'today'));
                $this->line("  📧 Sent TODAY activity reminder for: {$activity->subject}");
            }
            $sent++;
        }

        // Upcoming activities
        $upcomingActivities = Activity::upcoming($days)
            ->with(['user', 'related'])
            ->whereHas('user')
            ->where('scheduled_at', '>', now()->endOfDay()) // Exclude today
            ->get();

        foreach ($upcomingActivities as $activity) {
            if ($dryRun) {
                $this->line("  [DRY RUN] Would send UPCOMING activity reminder for: {$activity->subject} (Due: {$activity->scheduled_at->format('M j, Y g:i A')})");
            } else {
                $activity->user->notify(new ActivityReminder($activity, 'upcoming'));
                $this->line("  📧 Sent UPCOMING activity reminder for: {$activity->subject}");
            }
            $sent++;
        }

        $this->info("  📅 Activity reminders: {$sent}");
        return $sent;
    }
}
