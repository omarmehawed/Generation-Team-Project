<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            // بنعدل العمود عشان يقبل قيمة افتراضية 1
            // يعني لو مبعتناش كمية، اعتبرها قطعة واحدة
            if (Schema::hasColumn('project_expenses', 'quantity')) {
                $table->integer('quantity')->default(1)->change();
            } else {
                // احتياطي: لو العمود مش موجود أصلاً، نكريته
                $table->integer('quantity')->default(1)->after('item');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            // نرجعه زي ما كان (من غير default)
            $table->integer('quantity')->change();
        });
    }
};
