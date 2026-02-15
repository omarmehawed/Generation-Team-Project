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
        Schema::create('weekly_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->integer('week_number');
            $table->tinyInteger('commitment_level')->unsigned();
            $table->tinyInteger('satisfaction_level')->unsigned();
            $table->text('general_notes')->nullable();
            $table->string('pdf_path')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            // Ensure one evaluation per student per week
            $table->unique(['student_id', 'week_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_evaluations');
    }
};
