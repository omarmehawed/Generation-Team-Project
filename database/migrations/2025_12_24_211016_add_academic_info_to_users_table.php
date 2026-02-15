<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // سنة دراسية (1, 2, 3, 4) - افتراضياً سنة 1
            $table->tinyInteger('academic_year')->default(1)->after('role');

            // القسم (general, software, network) - افتراضياً general
            $table->enum('department', ['general', 'software', 'network'])->default('general')->after('academic_year');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['academic_year', 'department']);
        });
    }
};
