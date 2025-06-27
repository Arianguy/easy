<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['email', 'sms', 'social_media', 'print', 'event', 'digital_ads', 'other'])->default('email');
            $table->enum('status', ['draft', 'active', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->decimal('actual_cost', 15, 2)->nullable();
            $table->integer('target_audience')->default(0);
            $table->integer('reached_audience')->default(0);
            $table->integer('leads_generated')->default(0);
            $table->integer('conversions')->default(0);
            $table->json('metrics')->nullable(); // Store additional metrics
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['branch_id', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
