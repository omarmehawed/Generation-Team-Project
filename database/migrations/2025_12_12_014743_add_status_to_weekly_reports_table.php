<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('weekly_reports', function (Blueprint $table) {
        if (!Schema::hasColumn('weekly_reports', 'status')) {
            $table->string('status')->default('pending'); // or enum('pending', 'reviewed')
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_reports', function (Blueprint $table) {
            //
        });
    }
};
