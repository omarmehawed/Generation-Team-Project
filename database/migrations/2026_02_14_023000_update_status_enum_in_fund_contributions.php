<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE fund_contributions MODIFY COLUMN status ENUM('pending', 'paid', 'overdue', 'pending_approval', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting to the old enum values might lose data if there are records with new statuses.
        // We will just leave it or strictly revert if forced.
        // For safety, we keep the new values in down() or just do nothing/revert to subset.
        // Reverting to strict subset is dangerous if data exists.
        DB::statement("ALTER TABLE fund_contributions MODIFY COLUMN status ENUM('pending', 'paid', 'overdue') DEFAULT 'pending'");
    }
};
