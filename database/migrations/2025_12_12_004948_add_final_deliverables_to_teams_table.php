<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('teams', function (Blueprint $table) {
        // لو العواميد مش موجودة بنضيفها
        if (!Schema::hasColumn('teams', 'final_book_file')) {
            $table->string('final_book_file')->nullable();
        }
        if (!Schema::hasColumn('teams', 'presentation_file')) {
            $table->string('presentation_file')->nullable();
        }
        if (!Schema::hasColumn('teams', 'defense_video_link')) {
            $table->string('defense_video_link')->nullable();
        }
        if (!Schema::hasColumn('teams', 'is_fully_submitted')) {
            $table->boolean('is_fully_submitted')->default(false);
        }
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
