<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'lost', 'converted'])->default('new');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('source', ['walk_in', 'referral', 'online', 'campaign', 'cold_call', 'other'])->default('walk_in');
            $table->date('follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->date('expected_close_date')->nullable();
            $table->json('tags')->nullable();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['branch_id', 'status']);
            $table->index(['assigned_user_id', 'status']);
            $table->index(['follow_up_date']);
            $table->index(['expected_close_date']);
            $table->index(['source']);
            $table->index(['priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
