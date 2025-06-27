<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Add new columns (keep existing ones for now)
            $table->string('code', 10)->nullable()->after('name');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('state');
            $table->string('country', 100)->nullable()->after('postal_code');
            $table->string('phone', 20)->nullable()->after('country');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('email');
            $table->text('description')->nullable()->after('status');

            // Add new indexes
            $table->index(['status']);
        });

        // Add unique constraint for code (allowing nulls)
        Schema::table('branches', function (Blueprint $table) {
            $table->unique('code');
        });

        // Update existing records to have active status
        DB::table('branches')->update(['status' => 'active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Drop new indexes and constraints
            $table->dropIndex(['status']);
            $table->dropUnique(['code']);

            // Remove new columns
            $table->dropColumn([
                'code',
                'city',
                'state',
                'postal_code',
                'country',
                'phone',
                'status',
                'description'
            ]);
        });
    }
};
