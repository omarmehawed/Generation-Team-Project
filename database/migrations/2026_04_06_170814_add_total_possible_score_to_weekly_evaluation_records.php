<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('weekly_evaluation_records', function (Blueprint $table) {
            $table->decimal('total_possible_score', 5, 2)->default(30.00)->after('total_overall_score');
            $table->string('active_categories')->nullable()->after('total_possible_score'); // e.g. "tasks,meetings"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_evaluation_records', function (Blueprint $table) {
            $table->dropColumn('total_possible_score');
        });
    }
};
