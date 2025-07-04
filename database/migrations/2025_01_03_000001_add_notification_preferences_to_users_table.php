<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('email_reminders')->default(true)->after('last_login_at');
            $table->boolean('lead_reminders')->default(true)->after('email_reminders');
            $table->boolean('activity_reminders')->default(true)->after('lead_reminders');
            $table->boolean('overdue_notifications')->default(true)->after('activity_reminders');
            $table->boolean('upcoming_notifications')->default(true)->after('overdue_notifications');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_reminders',
                'lead_reminders',
                'activity_reminders',
                'overdue_notifications',
                'upcoming_notifications'
            ]);
        });
    }
};
