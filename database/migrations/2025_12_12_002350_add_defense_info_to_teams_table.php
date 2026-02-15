<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('teams', function (Blueprint $table) {
        $table->dateTime('defense_date')->nullable(); // التاريخ والساعة
        $table->string('defense_location')->nullable(); // القاعة أو المعمل
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            //
        });
    }
};
