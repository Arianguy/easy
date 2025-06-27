<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->text('interests')->nullable();
            $table->text('address')->nullable();
            $table->string('company')->nullable();
            $table->decimal('budget_range', 15, 2)->nullable();
            $table->enum('source', ['walk_in', 'referral', 'online', 'campaign', 'cold_call', 'other'])->default('walk_in');
            $table->enum('status', ['active', 'inactive', 'potential'])->default('potential');
            $table->date('last_contact_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['branch_id', 'created_at']);
            $table->index(['email']);
            $table->index(['mobile']);
            $table->index(['status']);
            $table->index(['source']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
