<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // سنة المادة (1, 2, 3, 4)
            $table->tinyInteger('year_level')->default(1);

            // الترم (1, 2)
            $table->tinyInteger('term')->default(1);

            // تخصص المادة (general, software, network)
            $table->enum('department', ['general', 'software', 'network'])->default('general');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['year_level', 'term', 'department']);
        });
    }
};
