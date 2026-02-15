<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            
            // 1. نتأكد إن عمود ملف التسليم مش موجود قبل ما نضيفه
            if (!Schema::hasColumn('tasks', 'submission_file')) {
                $table->string('submission_file')->nullable(); 
            }

            // 2. نتأكد إن عمود التعليق مش موجود
            if (!Schema::hasColumn('tasks', 'submission_comment')) {
                $table->text('submission_comment')->nullable(); 
            }

            // 3. نتأكد إن عمود تاريخ التسليم مش موجود
            if (!Schema::hasColumn('tasks', 'submitted_at')) {
                $table->dateTime('submitted_at')->nullable(); 
            }

            // 4. تحديث حالة الـ Status (دي بنسيبها change زي ما هي طالما العمود موجود)
            if (Schema::hasColumn('tasks', 'status')) {
                $table->enum('status', ['pending', 'reviewing', 'completed', 'rejected'])
                      ->default('pending')
                      ->change(); 
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // 
        });
    }
};