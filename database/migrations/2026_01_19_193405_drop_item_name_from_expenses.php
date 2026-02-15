<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            // بنمسح العمود القديم اللي عامل المشكلة
            if (Schema::hasColumn('project_expenses', 'item_name')) {
                $table->dropColumn('item_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->string('item_name')->nullable(); // نرجعه لو حبينا نرجع في كلامنا
        });
    }
};
