<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add shuffle settings + make fullscreen optional to quizzes table
        Schema::table('quizzes', function (Blueprint $table) {
            $table->boolean('shuffle_questions')->default(false)->after('require_fullscreen');
            $table->boolean('shuffle_options')->default(false)->after('shuffle_questions');
        });

        // Save the per-attempt question order (generated once at start)
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->json('question_order')->nullable()->after('current_step');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['shuffle_questions', 'shuffle_options']);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn('question_order');
        });
    }
};
