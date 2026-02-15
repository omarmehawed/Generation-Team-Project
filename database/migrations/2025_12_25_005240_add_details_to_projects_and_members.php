<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. إضافة ديدلاين الخروج للمشروع (بأمان)
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'leave_team_deadline')) {
                $table->timestamp('leave_team_deadline')->nullable()->after('deadline');
            }
        });

        // 2. إضافة خانة "الدرجة الفردية" (بأمان)
        Schema::table('team_members', function (Blueprint $table) {
            if (!Schema::hasColumn('team_members', 'individual_score')) {
                $table->decimal('individual_score', 5, 2)->nullable()->after('role');
            }
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('leave_team_deadline');
        });
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn('individual_score');
        });
    }
};
