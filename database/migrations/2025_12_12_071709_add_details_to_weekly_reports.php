<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('weekly_reports', function (Blueprint $table) {
        // بنضيف الأعمدة الناقصة بناء على الكود بتاعك
        if (!Schema::hasColumn('weekly_reports', 'report_date')) {
            $table->date('report_date')->nullable();
        }
        if (!Schema::hasColumn('weekly_reports', 'achievements')) {
            $table->text('achievements')->nullable();
        }
        if (!Schema::hasColumn('weekly_reports', 'plans')) {
            $table->text('plans')->nullable();
        }
        if (!Schema::hasColumn('weekly_reports', 'challenges')) {
            $table->text('challenges')->nullable();
        }
        // تأكد إن status موجودة
        if (!Schema::hasColumn('weekly_reports', 'status')) {
            $table->string('status')->default('pending');
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
