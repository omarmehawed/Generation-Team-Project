<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            
            // 1. ملف "الكتاب" (PDF)
            if (!Schema::hasColumn('projects', 'final_book_file')) {
                $table->string('final_book_file')->nullable(); 
            }

            // 2. ملف "العرض التقديمي" (PPTX/PDF)
            if (!Schema::hasColumn('projects', 'presentation_file')) {
                $table->string('presentation_file')->nullable(); 
            }

            // 3. رابط فيديو المناقشة (YouTube/Drive Link)
            if (!Schema::hasColumn('projects', 'defense_video_link')) {
                $table->string('defense_video_link')->nullable(); 
            }

            // 4. نضيف حالة "مكتمل" (Completed) للـ Enum بتاع الحالة
            // ملحوظة: تعديل الـ Enum في Laravel بيحتاج تعامل خاص، 
            // بس للسهولة هنعتمد إننا نغير القيمة في الكود عادي لو العمود بيقبل String
            // أو ممكن نستخدم عمود جديد للتسليم النهائي
            if (!Schema::hasColumn('projects', 'is_fully_submitted')) {
                $table->boolean('is_fully_submitted')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            //
        });
    }
};