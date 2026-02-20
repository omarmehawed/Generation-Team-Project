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
        // 1. Add 'leader_b' to the ENUM
        DB::statement("ALTER TABLE team_members MODIFY COLUMN role ENUM('leader', 'leader_b', 'vice_leader', 'head_sw', 'head_hw', 'member_sw', 'member_hw', 'member') DEFAULT 'member'");

        // 2. Ensure main 'leader' is automatically assigned to Group A
        DB::table('team_members')->where('role', 'leader')->update([
            'is_group_a' => true,
            'is_group_b' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_members_role_enum', function (Blueprint $table) {
            //
        });
    }
};
