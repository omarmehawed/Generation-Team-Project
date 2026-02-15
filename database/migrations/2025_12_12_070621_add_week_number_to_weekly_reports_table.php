<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('weekly_reports', function (Blueprint $table) {
        
        // 1. إضافة week_number (بنتأكد إنه مش موجود الأول عشان ميعملش error)
        if (!Schema::hasColumn('weekly_reports', 'week_number')) {
            $table->integer('week_number')->nullable()->after('id');
        }

        // 2. التعامل مع Title
        if (Schema::hasColumn('weekly_reports', 'title')) {
            // لو موجود.. خليه يقبل قيم فارغة
            $table->string('title')->nullable()->change();
        } else {
            // لو مش موجود.. أنشئه وخليه يقبل قيم فارغة
            $table->string('title')->nullable();
        }
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_reports', function (Blueprint $table) {
            //
        });
    }
};
