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
        Schema::dropIfExists('evaluation_items');
        Schema::dropIfExists('weekly_evaluations');

        Schema::create('weekly_evaluation_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->integer('week_number');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('open'); // open, closed
            $table->timestamps();
        });

        Schema::create('weekly_evaluation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_period_id')->constrained('weekly_evaluation_periods')->cascadeOnDelete();
            $table->foreignId('evaluatee_id')->constrained('team_members')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('team_members')->cascadeOnDelete();
            
            $table->decimal('total_task_score', 5, 2)->default(0);
            $table->decimal('total_workshop_score', 5, 2)->default(0);
            $table->decimal('total_meeting_score', 5, 2)->default(0);
            $table->decimal('total_overall_score', 5, 2)->default(0);
            
            $table->text('general_notes')->nullable();
            $table->string('evaluation_type')->default('member'); // member, sub_leader, vice_leader
            $table->timestamps();
        });

        Schema::create('weekly_evaluation_sub_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_evaluation_record_id')->constrained('weekly_evaluation_records')->cascadeOnDelete();
            $table->string('source_type'); // task, workshop, meeting
            $table->unsignedBigInteger('source_id'); // e.g., task_id or workshop_id
            $table->decimal('score', 5, 2)->default(0);
            $table->string('penalty_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_evaluation_sub_items');
        Schema::dropIfExists('weekly_evaluation_records');
        Schema::dropIfExists('weekly_evaluation_periods');
    }
};
