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
        Schema::table('activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_logs', 'subject_type')) {
                $table->string('subject_type')->nullable()->after('subject_id');
            }
            if (!Schema::hasColumn('activity_logs', 'team_id')) {
                $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete()->after('subject_type');
            }
            if (!Schema::hasColumn('activity_logs', 'target_user_id')) {
                $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete()->after('team_id');
            }
            if (!Schema::hasColumn('activity_logs', 'description')) {
                $table->text('description')->nullable()->after('action');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['subject_type', 'team_id', 'target_user_id', 'description']);
        });
    }
};
