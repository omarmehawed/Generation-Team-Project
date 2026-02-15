<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('tasks', function (Blueprint $table) {
        // بنقوله: لو العمود مش موجود.. ضيفه
        if (!Schema::hasColumn('tasks', 'description')) {
            $table->text('description')->nullable()->after('title');
        }
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
        });
    }
};
