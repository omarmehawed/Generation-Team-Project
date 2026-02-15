<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // العلاقات الأساسية
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // الطالب المسئول
            $table->foreignId('creator_id')->nullable()->constrained('users')->onDelete('set null'); // مين اللي عمل التاسك

            // تفاصيل التاسك
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('deadline')->nullable();

            // الحالة
            $table->enum('status', ['pending', 'reviewing', 'completed', 'rejected', 'returned'])->default('pending');

            // بيانات التسليم (Submission)
            $table->string('submission_type')->nullable(); // 'file' or 'link'
            $table->text('submission_value')->nullable(); // The link itself
            $table->string('submission_file')->nullable(); // File path
            $table->text('submission_comment')->nullable();
            $table->timestamp('submitted_at')->nullable();

            // بيانات التصحيح (Grading)
            $table->decimal('grade', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
