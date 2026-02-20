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
            $table->dropColumn('auto_group');
            $table->boolean('is_group_a')->default(false)->after('role');
            $table->boolean('is_group_b')->default(false)->after('is_group_a');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['is_group_a', 'is_group_b']);
            $table->enum('auto_group', ['A', 'B'])->nullable()->after('role');
        });
    }
};
