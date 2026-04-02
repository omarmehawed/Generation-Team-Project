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
        Schema::table('team_members', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('team_members')->onDelete('set null')->after('is_vice_leader');
            $table->integer('team_number')->nullable()->after('parent_id');
            // Since role is Enum in old files, we don't redefine the enum in SQLite/MySQL easily without dropping. 
            // Instead of dropping, we can just use `extra_role` or a new boolean `is_sub_leader` to avoid enum complications.
            // Wait, the easiest way to avoid enum modifying errors on different DB engines is adding a boolean or extending it via DB statement if MySQL.
            // Let's add `is_sub_leader` boolean similar to `is_vice_leader`.
            $table->boolean('is_sub_leader')->default(false)->after('team_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'team_number', 'is_sub_leader']);
        });
    }
};
