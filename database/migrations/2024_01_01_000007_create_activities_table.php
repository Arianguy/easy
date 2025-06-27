<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // call, email, meeting, note, task
            $table->string('subject');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->datetime('scheduled_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->enum('outcome', ['successful', 'unsuccessful', 'rescheduled', 'no_response'])->nullable();
            $table->morphs('related'); // Can be related to customer, lead, or opportunity
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'scheduled_at']);
            $table->index(['branch_id', 'type']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
