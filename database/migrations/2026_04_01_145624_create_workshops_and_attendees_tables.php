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
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            
            $table->string('title');
            $table->date('workshop_date');
            $table->time('workshop_time');
            $table->string('type')->default('offline'); // online, offline
            $table->string('location_or_link')->nullable(); // GMeet link or Room number
            $table->string('domain')->default('general'); // software, hardware, general
            
            $table->timestamps();
        });

        Schema::create('workshop_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained('workshops')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            $table->string('status')->default('pending'); // attended, absent, late, pending
            $table->decimal('participation_score', 5, 2)->default(0);
            $table->text('files_uploaded')->nullable(); // Can store multiple URLs or a JSON
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_attendees');
        Schema::dropIfExists('workshops');
    }
};
