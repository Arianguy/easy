<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send follow-up reminders every hour during business hours (9 AM to 6 PM)
        $schedule->command('reminders:send --type=all --days=1')
            ->hourly()
            ->between('9:00', '18:00')
            ->weekdays()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/reminders.log'));

        // Send overdue reminders every 2 hours during business hours
        $schedule->command('reminders:send --type=all --days=0')
            ->cron('0 */2 9-18 * * 1-5') // Every 2 hours, 9 AM to 6 PM, weekdays
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/reminders.log'));

        // Send upcoming reminders (3 days ahead) once daily at 8 AM
        $schedule->command('reminders:send --type=all --days=3')
            ->dailyAt('08:00')
            ->weekdays()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/reminders.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
