<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('tasks', function (Blueprint $table) {
        
        // لو العمود مش موجود.. ضيفه
        if (!Schema::hasColumn('tasks', 'assigned_by')) {
            // بنعمله Nullable عشان لو حبينا في المستقبل التاسك يكون من غير مشرف
            // وبنربطه بجدول الـ users
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
        });
    }
};
