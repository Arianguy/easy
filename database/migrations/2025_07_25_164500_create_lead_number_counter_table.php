<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lead_number_counter', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('next_number')->default(1);
            $table->timestamps();
        });

        // Insert the initial counter
        \DB::table('lead_number_counter')->insert([
            'next_number' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_number_counter');
    }
};
