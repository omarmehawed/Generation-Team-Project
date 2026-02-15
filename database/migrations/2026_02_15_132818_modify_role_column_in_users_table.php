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
        Schema::table('users', function (Blueprint $table) {
            // Change the column to string (varchar) to fix truncation/enum issues
            // This requires doctrine/dbal, but recent Laravel versions support it natively for MySQL/MariaDB
            $table->string('role', 50)->default('student')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert to enum (if needed, but string is generally better)
            // But we can't easily revert to enum if there are invalid values, so we might just leave it or try to revert
            // For safety, we will just revert it to the specific ENUM if possible, or string(255)
             $table->enum('role', ['student', 'doctor', 'ta'])->default('student')->change();
        });
    }
};
