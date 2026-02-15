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
        Schema::create('evaluation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_evaluation_id')->constrained('weekly_evaluations')->cascadeOnDelete();
            $table->string('type'); // task, quiz, meeting, workshop, activity
            $table->string('title');
            $table->string('rating')->nullable(); // poor, average, good, etc.
            $table->integer('mark')->nullable(); // For quizzes
            $table->text('note')->nullable(); // For tasks
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_items');
    }
};
