<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('meetings', function (Blueprint $table) {
        if (!Schema::hasColumn('meetings', 'description')) {
            $table->text('description')->nullable(); // سبب الاجتماع
        }
        if (!Schema::hasColumn('meetings', 'meeting_link')) {
            $table->string('meeting_link')->nullable(); // لينك الزووم أو تيمز
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            //
        });
    }
};
