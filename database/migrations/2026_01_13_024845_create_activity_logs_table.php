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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('causer_id')->nullable()->constrained('users')->nullOnDelete(); // مين الأدمن اللي عمل الفعل
            $table->foreignId('subject_id')->nullable()->constrained('users')->nullOnDelete(); // مين اليوزر اللي اتعدل عليه
            $table->string('action'); // نوع الفعل (create, update, delete)
            $table->json('changes')->nullable(); // التغييرات (Old vs New)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
