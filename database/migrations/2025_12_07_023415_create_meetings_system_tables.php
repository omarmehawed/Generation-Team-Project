<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. جدول الاجتماعات
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->string('topic'); // عنوان الاجتماع
            $table->dateTime('meeting_date'); // الميعاد
            $table->enum('mode', ['online', 'offline']); // أونلاين ولا في الكلية
            $table->string('meeting_link')->nullable(); // لينك زوم أو جوجل ميت
            $table->enum('type', ['supervision', 'internal'])->default('supervision'); // نوع الاجتماع
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled' ])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. جدول الحضور والغياب
        Schema::create('meeting_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // اسم الطالب
            $table->boolean('is_present')->default(false); // حضر ولا لأ
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meeting_attendances');
        Schema::dropIfExists('meetings');
    }
};