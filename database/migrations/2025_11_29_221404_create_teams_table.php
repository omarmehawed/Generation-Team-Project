<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade'); // ربط بالمشروع
            $table->string('name'); // اسم التيم
            $table->string('code')->unique(); // كود الانضمام (لازم يكون مميز)
            $table->foreignId('leader_id')->constrained('users'); // ربط بالطالب الليدر
            $table->enum('status', ['forming', 'locked', 'submitted', 'completed'])->default('forming');
            

                // --- الجزء اللي ناقص ---
            $table->string('submission_path')->nullable(); // مكان الملف
            
            $table->text('submission_comment')->nullable(); // تعليق الطالب
            $table->integer('grade')->nullable(); // الدرجة
            $table->text('doctor_feedback')->nullable(); // تعليق الدكتور
            // ---------------------
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
