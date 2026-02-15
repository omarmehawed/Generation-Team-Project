<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 👇 الشرط ده بيمنع الإيرور: لو الجدول مش موجود، اعمله. لو موجود، كمل عادي.
        if (!Schema::hasTable('project_expenses')) {
            Schema::create('project_expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // الطالب اللي اشترى
                $table->string('item'); // اسم الحاجة (مثلاً: Arduino, Sensor)
                $table->decimal('amount', 10, 2); // السعر
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('project_expenses');
    }
};
