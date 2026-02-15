<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // اسم الإعداد (مثلاً current_term)
            $table->string('value');         // قيمته (مثلاً 1 أو 2)
            $table->timestamps();
        });

        // إضافة القيمة الافتراضية فوراً
        DB::table('settings')->insert([
            'key' => 'current_term',
            'value' => '1'
        ]);
    }
};
