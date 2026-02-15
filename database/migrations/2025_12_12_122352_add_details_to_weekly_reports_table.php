<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('weekly_reports', function (Blueprint $table) {
        // Add week_number if it doesn't exist
        if (!Schema::hasColumn('weekly_reports', 'week_number')) {
            $table->integer('week_number')->after('team_id');
        }

        // Add report_date
        if (!Schema::hasColumn('weekly_reports', 'report_date')) {
            $table->dateTime('report_date')->nullable()->after('week_number');
        }

        // Add text fields
        if (!Schema::hasColumn('weekly_reports', 'achievements')) {
            $table->text('achievements')->nullable();
        }
        if (!Schema::hasColumn('weekly_reports', 'plans')) {
            $table->text('plans')->nullable();
        }
        if (!Schema::hasColumn('weekly_reports', 'challenges')) {
            $table->text('challenges')->nullable();
        }

        // Ensure status column exists
        if (!Schema::hasColumn('weekly_reports', 'status')) {
            $table->string('status')->default('pending');
        }

        // Make title nullable since we use week_number now
        if (Schema::hasColumn('weekly_reports', 'title')) {
            $table->string('title')->nullable()->change();
        }
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_reports', function (Blueprint $table) {
            //
        });
    }
};
