<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('team_id')->constrained()->onDelete('cascade');
        $table->foreignId('reporter_id')->constrained('users'); // الليدر اللي بلغ
        $table->foreignId('reported_user_id')->constrained('users')->onDelete('cascade'); // العضو المبلغ عنه
        $table->text('reason'); // تفاصيل المشكلة
        $table->string('link')->nullable(); // جيت هاب مثلاً
        $table->string('file_path')->nullable(); // صور شات او ملفات
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
