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
        // 1. Reassign anyone holding the 'leader_b' role to 'member'
        DB::table('team_members')->where('role', 'leader_b')->update(['role' => 'member']);
        
        // 2. Safely alter the ENUM column back to its original state (removing leader_b)
        DB::statement("ALTER TABLE team_members MODIFY COLUMN role ENUM('leader', 'vice_leader', 'head_sw', 'head_hw', 'member_sw', 'member_hw', 'member') DEFAULT 'member'");

        // 3. Drop the group flags
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['is_group_a', 'is_group_b']);
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
