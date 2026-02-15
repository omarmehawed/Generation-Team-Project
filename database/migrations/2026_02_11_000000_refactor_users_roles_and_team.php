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
        // 1. Update structure first
        Schema::table('users', function (Blueprint $table) {
            // Drop old enum if possible or just modify it raw.
            // On MySQL, strict enum alteration is hard without raw SQL.
            // We'll add 'admin' and 'member' to the allowed list first if possible, 
            // but Laravel enum modification is tricky.
            // Safest way involves raw SQL for ENUMs.
            
            // We will attempt to modify the column to string first to remove enum constraint,
            // update values, then set it back to new enum or just string.
            // Given the request "convert to Admin/Member", we'll just open it up to string first.
            $table->string('role', 50)->change();
            
            // Add team_id
            if (!Schema::hasColumn('users', 'team_id')) {
                $table->foreignId('team_id')->nullable()->after('id'); // No constraint yet to avoid breaking
            }
        });

        // 2. Data Migration
        // Doctor -> Admin
        DB::table('users')->where('role', 'doctor')->update(['role' => 'admin']);
        // Student -> Member
        DB::table('users')->where('role', 'student')->update(['role' => 'member']);

        // 3. Enforce new ENUM if widely supported, or just leave as string with validation in app.
        // The user request explicitly mentions "Admin" and "Member".
        // Let's modify it back to ENUM with new values.
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'member', 'ta', 'user'])->default('member')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert is hard because data might be lost/incompatible.
            // We'll just generic revert to string.
            $table->string('role', 255)->change();
            $table->dropColumn('team_id');
        });
    }
};
