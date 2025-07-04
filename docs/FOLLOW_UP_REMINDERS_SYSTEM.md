# Follow-Up Reminders & Notification System

## Overview

The CRM now includes a comprehensive follow-up reminder and notification system that automatically alerts users about upcoming and overdue follow-ups for leads and activities.

## Features

### ✅ What's Implemented

1. **Email & Database Notifications**

    - Lead follow-up reminders
    - Activity schedule reminders
    - Overdue notifications
    - Upcoming notifications

2. **User Notification Preferences**

    - Granular control over notification types
    - Email notification toggle
    - Separate settings for leads and activities
    - Overdue vs upcoming notification preferences

3. **Automated Scheduling**

    - Runs automatically during business hours
    - Multiple reminder frequencies
    - Prevents duplicate notifications

4. **Dashboard Integration**
    - Today's reminders section
    - Upcoming reminders section
    - Enhanced overdue tracking

## System Components

### 1. Notification Classes

#### `app/Notifications/FollowUpReminder.php`

-   Handles lead follow-up reminders
-   Supports three types: 'overdue', 'today', 'upcoming'
-   Respects user notification preferences
-   Sends both email and database notifications

#### `app/Notifications/ActivityReminder.php`

-   Handles activity schedule reminders
-   Same type system as follow-up reminders
-   Includes related model information

### 2. Console Command

#### `app/Console/Commands/SendFollowUpReminders.php`

-   Main command for processing reminders
-   Supports dry-run mode for testing
-   Configurable reminder types and timeframes

**Usage:**

```bash
# Send all reminders (default)
php artisan reminders:send

# Send only lead reminders
php artisan reminders:send --type=leads

# Send only activity reminders
php artisan reminders:send --type=activities

# Check 3 days ahead for upcoming reminders
php artisan reminders:send --days=3

# Dry run to see what would be sent
php artisan reminders:send --dry-run
```

### 3. Scheduled Tasks

#### `app/Console/Kernel.php`

Configured with three different schedules:

1. **Hourly Reminders** (9 AM - 6 PM, weekdays)

    ```php
    $schedule->command('reminders:send --type=all --days=1')
        ->hourly()
        ->between('9:00', '18:00')
        ->weekdays();
    ```

2. **Overdue Reminders** (Every 2 hours, 9 AM - 6 PM, weekdays)

    ```php
    $schedule->command('reminders:send --type=all --days=0')
        ->cron('0 */2 9-18 * * 1-5');
    ```

3. **Daily Upcoming Reminders** (8 AM, weekdays)
    ```php
    $schedule->command('reminders:send --type=all --days=3')
        ->dailyAt('08:00')
        ->weekdays();
    ```

### 4. User Model Enhancements

#### `app/Models/User.php`

Added notification preference methods:

-   `wantsEmailReminders()`
-   `wantsLeadReminders()`
-   `wantsActivityReminders()`
-   `wantsOverdueNotifications()`
-   `wantsUpcomingNotifications()`

### 5. Database Migration

#### `database/migrations/2025_01_03_000001_add_notification_preferences_to_users_table.php`

Adds notification preference columns:

-   `email_reminders` (boolean, default: true)
-   `lead_reminders` (boolean, default: true)
-   `activity_reminders` (boolean, default: true)
-   `overdue_notifications` (boolean, default: true)
-   `upcoming_notifications` (boolean, default: true)

### 6. Settings Interface

#### `app/Livewire/Settings/NotificationPreferences.php`

-   User-friendly interface for managing notification preferences
-   Real-time toggle updates
-   Accessible via Settings → Notifications

## Setup Instructions

### 1. Run Migration

```bash
php artisan migrate
```

### 2. Configure Mail Settings

Update your `.env` file with mail configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=crm@yourcompany.com
MAIL_FROM_NAME="Your CRM System"
```

### 3. Configure Queue Processing

For production, set up queue processing:

```env
QUEUE_CONNECTION=database
```

Start the queue worker:

```bash
php artisan queue:work --daemon
```

### 4. Set Up Cron Job

Add this to your server's crontab:

```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### 5. Test the System

```bash
# Test with dry run
php artisan reminders:send --dry-run

# Send actual reminders
php artisan reminders:send
```

## Usage Guide

### For Users

1. **Set Notification Preferences**

    - Go to Settings → Notifications
    - Toggle preferences as desired
    - Save changes

2. **View Reminders**
    - Check dashboard for today's and upcoming reminders
    - Receive email notifications based on preferences
    - View notification history in database

### For Administrators

1. **Monitor Reminder Logs**

    ```bash
    tail -f storage/logs/reminders.log
    ```

2. **Manually Send Reminders**

    ```bash
    php artisan reminders:send --type=all
    ```

3. **Check Queue Status**
    ```bash
    php artisan queue:work --once
    ```

## Notification Types & Timing

### Lead Follow-up Reminders

| Type     | When Sent      | Frequency                      |
| -------- | -------------- | ------------------------------ |
| Overdue  | Past due date  | Every 2 hours (business hours) |
| Today    | Due today      | Hourly (business hours)        |
| Upcoming | 1-3 days ahead | Daily at 8 AM                  |

### Activity Reminders

| Type     | When Sent           | Frequency                      |
| -------- | ------------------- | ------------------------------ |
| Overdue  | Past scheduled time | Every 2 hours (business hours) |
| Today    | Due today           | Hourly (business hours)        |
| Upcoming | 1-3 days ahead      | Daily at 8 AM                  |

## Email Templates

The system uses Laravel's built-in mail templates with:

-   Professional styling
-   CRM branding
-   Action buttons to view items
-   Contextual information (priority, dates, etc.)

## Troubleshooting

### Common Issues

1. **Emails Not Sending**

    - Check mail configuration in `.env`
    - Verify queue is processing: `php artisan queue:work`
    - Check mail logs: `tail -f storage/logs/laravel.log`

2. **Reminders Not Running**

    - Verify cron job is set up correctly
    - Check scheduler: `php artisan schedule:list`
    - Run manually: `php artisan schedule:run`

3. **Database Notifications Not Appearing**
    - Ensure notifications table exists
    - Check user preferences
    - Verify command is running without errors

### Debug Commands

```bash
# Check what reminders would be sent
php artisan reminders:send --dry-run

# Test specific reminder types
php artisan reminders:send --type=leads --dry-run

# Check scheduler
php artisan schedule:list

# Run scheduler manually
php artisan schedule:run

# Process queue manually
php artisan queue:work --once
```

## Customization

### Modify Notification Content

Edit the notification classes:

-   `app/Notifications/FollowUpReminder.php`
-   `app/Notifications/ActivityReminder.php`

### Change Scheduling

Modify `app/Console/Kernel.php` to adjust:

-   Reminder frequency
-   Business hours
-   Days of week

### Add New Reminder Types

1. Create new notification class
2. Update console command
3. Add user preferences
4. Update settings interface

## Security Considerations

-   Notifications respect branch permissions
-   Users only receive reminders for their assigned items
-   Email addresses are validated before sending
-   Notification preferences are user-specific

## Performance Notes

-   Commands use query optimization with eager loading
-   Notifications are queued for background processing
-   Duplicate prevention with `withoutOverlapping()`
-   Logs are rotated automatically

## Future Enhancements

Potential improvements:

-   SMS notifications
-   Slack integration
-   Custom reminder intervals
-   Notification escalation
-   Team notifications
-   Mobile push notifications
