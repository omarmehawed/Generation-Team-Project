<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('weekly_reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('team_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained(); // مين اللي كتب التقرير
        $table->integer('week_number'); // رقم الأسبوع (1, 2, 3...)
        $table->text('achievements'); // إنجازات الأسبوع
        $table->text('plans'); // خطة الأسبوع الجاي
        $table->text('challenges')->nullable(); // مشاكل واجهتنا
        $table->string('file_path')->nullable(); // لو فيه ملف مرفق
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_reports');
    }
};
