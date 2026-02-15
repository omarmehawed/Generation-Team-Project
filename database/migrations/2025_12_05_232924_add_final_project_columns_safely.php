<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// هنا سمينا الكلاس بنفس اسم الملف عشان الايرور يروح
class AddFinalProjectColumnsSafely extends Migration
{
    public function up()
    {
        // 1. تعديل جدول التيمات (بأمان تام)
        Schema::table('teams', function (Blueprint $table) {
            $table->string('proposal_status')->nullable()->default(null);
            $table->string('project_phase')->nullable();
            $table->foreignId('ta_id')->nullable()->constrained('users')->onDelete('set null');
        });

        // 2. تعديل جدول الأعضاء
        Schema::table('team_members', function (Blueprint $table) {
            $table->string('sub_team')->nullable();
            $table->boolean('is_vice_leader')->default(false);
        });

        // 3. إنشاء جدول المصاريف
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->string('item_name');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->string('receipt_image');
            $table->timestamps();
        });

        // 4. إنشاء جدول الأرشيف
        Schema::create('project_gallery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->string('file_path');
            $table->string('type'); // image, video
            $table->string('caption')->nullable();
            $table->foreignId('uploaded_by')->constrained('users'); // ضفتلك دي عشان نعرف مين رفع الصورة
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_gallery');
        Schema::dropIfExists('project_expenses');
        
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['sub_team', 'is_vice_leader']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['proposal_status', 'project_phase', 'ta_id']);
        });
    }
}