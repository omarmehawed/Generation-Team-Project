<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('team_members', function (Blueprint $table) {
        if (Schema::hasColumn('team_members', 'is_group_a')) {
            $table->dropColumn('is_group_a');
        }
        if (Schema::hasColumn('team_members', 'is_group_b')) {
            $table->dropColumn('is_group_b');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Re-add the group flags
        Schema::table('team_members', function (Blueprint $table) {
            $table->boolean('is_group_a')->default(false)->after('role');
            $table->boolean('is_group_b')->default(false)->after('is_group_a');
        });

        // 2. Re-add 'leader_b' to the ENUM
        DB::statement("ALTER TABLE team_members MODIFY COLUMN role ENUM('leader', 'leader_b', 'vice_leader', 'head_sw', 'head_hw', 'member_sw', 'member_hw', 'member') DEFAULT 'member'");
    }
};
