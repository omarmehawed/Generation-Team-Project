<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('team_members', function (Blueprint $table) {
        // لو الأعمدة مش موجودة، ضيفها
        if (!Schema::hasColumn('team_members', 'technical_role')) {
            $table->string('technical_role')->default('general')->after('role'); // software, hardware, general
        }
        if (!Schema::hasColumn('team_members', 'extra_role')) {
            $table->string('extra_role')->nullable()->after('technical_role'); // presentation, reports, etc.
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            //
        });
    }
};
