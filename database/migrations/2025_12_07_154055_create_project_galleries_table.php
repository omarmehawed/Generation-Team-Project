<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::create('project_galleries', function (Blueprint $table) {
        $table->id();
        $table->foreignId('team_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained(); // مين اللي رفع
        $table->string('file_path');
        $table->string('caption')->nullable(); // وصف الصورة
        $table->enum('category', ['prototype', 'diagram', 'software', 'event', 'other'])->default('other');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_galleries');
    }
};
