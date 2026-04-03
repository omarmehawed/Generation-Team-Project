<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('technical_role')->nullable()->after('team_id');
            $table->text('rejection_feedback')->nullable()->after('feedback');
            $table->dateTime('new_deadline')->nullable()->after('deadline');
        });

        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('submission_type'); // file, link
            $table->text('submission_value')->nullable(); // link
            $table->string('submission_file')->nullable(); // path/url
            $table->text('submission_comment')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('feedback')->nullable();
            $table->dateTime('new_deadline')->nullable(); // for re-submissions
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_submissions');
    }
};
