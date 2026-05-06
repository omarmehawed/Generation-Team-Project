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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('created_by_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->dateTime('deadline')->nullable();
            $table->boolean('allow_edit_response')->default(false);
            $table->json('assigned_teams')->nullable(); // null means all
            $table->json('assigned_roles')->nullable(); // null means all
            $table->json('assigned_users')->nullable(); // null means all
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('form_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
            $table->string('title');
            $table->string('type'); // short_answer, paragraph, multiple_choice, checkboxes, dropdown, file_upload, date, time, rating
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable(); // for multiple choice, etc.
            $table->json('settings')->nullable(); // for extra constraints
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('form_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('submitted'); // draft, submitted
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('form_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_response_id')->constrained('form_responses')->onDelete('cascade');
            $table->foreignId('form_question_id')->constrained('form_questions')->onDelete('cascade');
            $table->text('answer_text')->nullable();
            $table->json('answer_json')->nullable(); // for multiple selections
            $table->string('answer_file')->nullable(); // path to file
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_answers');
        Schema::dropIfExists('form_responses');
        Schema::dropIfExists('form_questions');
        Schema::dropIfExists('forms');
    }
};
