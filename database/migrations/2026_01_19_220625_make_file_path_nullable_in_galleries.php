<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_galleries', function (Blueprint $table) {
            // بنعدل العمود عشان يقبل NULL (عشان حالة الفيديو)
            $table->string('file_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('project_galleries', function (Blueprint $table) {
            // لو حبينا نرجعه إجباري تاني
            $table->string('file_path')->nullable(false)->change();
        });
    }
};
