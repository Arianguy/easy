<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('value', 15, 2);
            $table->enum('stage', ['prospecting', 'proposal', 'negotiation', 'won', 'lost'])->default('prospecting');
            $table->decimal('probability', 5, 2)->default(0); // 0-100%
            $table->date('expected_close_date')->nullable();
            $table->date('actual_close_date')->nullable();
            $table->text('description')->nullable();
            $table->text('close_reason')->nullable(); // Why won/lost
            $table->json('products_services')->nullable(); // What they're buying
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['branch_id', 'stage']);
            $table->index(['expected_close_date']);
            $table->index(['actual_close_date']);
            $table->index(['stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
