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
        Schema::create('join_request_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_text');
            $table->string('question_type'); // text, textarea, radio, select, checkbox, date, number, matrix, scale
            $table->json('options')->nullable(); // For choices or matrix rows/cols
            $table->boolean('is_required')->default(true);
            $table->integer('order_priority')->default(0);
            $table->json('conditional_logic')->nullable(); // { "show_if_question_id": X, "show_if_value": "Y" }
            $table->string('placeholder')->nullable();
            $table->string('section')->default('technical'); // basic, technical
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('join_request_questions');
    }
};
