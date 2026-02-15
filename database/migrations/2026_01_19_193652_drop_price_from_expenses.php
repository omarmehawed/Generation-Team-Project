<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            // بنمسح عمود price لأنه زيادة واحنا بنستخدم amount بداله
            if (Schema::hasColumn('project_expenses', 'price')) {
                $table->dropColumn('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            // لو حبينا نرجعه (احتياطي)
            $table->decimal('price', 10, 2)->nullable();
        });
    }
};
