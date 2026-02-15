<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            // بنمسح العمود القديم عشان نعتمد receipt_path
            if (Schema::hasColumn('project_expenses', 'receipt_image')) {
                $table->dropColumn('receipt_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            // لو حبينا نرجعه
            $table->string('receipt_image')->nullable();
        });
    }
};
