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
        Schema::create('join_requests', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('full_name');
            $table->date('date_of_birth');
            $table->string('national_id');
            $table->string('academic_id');
            $table->enum('group', ['G1', 'G2', 'G3', 'G4']);
            $table->string('phone_number');
            $table->string('whatsapp_number');
            $table->string('photo_path')->nullable(); // Formal Photo

            // Section 2 Answers (Stored as JSON)
            $table->json('answers')->nullable(); 

            // Status and User Link
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('user_id')->nullable(); // Linked User after approval
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('join_requests');
    }
};
