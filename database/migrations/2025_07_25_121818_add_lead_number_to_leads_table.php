<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add lead_number column as nullable first
        Schema::table('leads', function (Blueprint $table) {
            $table->string('lead_number')->nullable()->after('id');
        });

        // Update existing records with LD- prefixed IDs
        $leads = DB::table('leads')->get();
        foreach ($leads as $lead) {
            DB::table('leads')
                ->where('id', $lead->id)
                ->update([
                    'lead_number' => 'LD-' . str_pad($lead->id, 5, '0', STR_PAD_LEFT)
                ]);
        }

        // Make the column not nullable and add unique constraint
        Schema::table('leads', function (Blueprint $table) {
            $table->string('lead_number')->nullable(false)->change();
            $table->unique('lead_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropUnique(['lead_number']);
            $table->dropColumn('lead_number');
        });
    }
};
