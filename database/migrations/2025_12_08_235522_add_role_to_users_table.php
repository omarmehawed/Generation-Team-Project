<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // بنضيف عمود الدور (Role) عشان نفرق بين الأنواع
            // student: طالب
            // doctor: دكتور (صلاحيات كاملة)
            // ta: معيد (صلاحيات متابعة)
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['student', 'doctor', 'ta'])
                      ->default('student')
                      ->after('email'); 
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};