<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('teams', function (Blueprint $table) {
        // أعمدة الدرجات (ممكن تعدلها حسب نظام الجامعة)
        $table->decimal('grade_phase1', 5, 2)->nullable(); // مثلاً درجة الفكرة
        $table->decimal('grade_midterm', 5, 2)->nullable(); // درجة الميدتيرم
        $table->decimal('grade_final', 5, 2)->nullable(); // درجة المناقشة النهائية
        $table->decimal('grade_total', 5, 2)->nullable(); // المجموع
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            //
        });
    }
};
