<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            // شلنا after عشان نريح دماغنا من ترتيب العواميد
            
            // حالة المشروع
            if (!Schema::hasColumn('projects', 'status')) {
                $table->enum('status', ['in_progress', 'pending_review', 'changes_requested', 'ready_for_defense'])
                      ->default('in_progress');
            }

            // تاريخ المعاد الحبي
            if (!Schema::hasColumn('projects', 'pre_defense_date')) {
                $table->dateTime('pre_defense_date')->nullable();
            }

            // ملاحظات الدكتور
            if (!Schema::hasColumn('projects', 'doctor_feedback')) {
                $table->text('doctor_feedback')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['status', 'pre_defense_date', 'doctor_feedback']);
        });
    }
};