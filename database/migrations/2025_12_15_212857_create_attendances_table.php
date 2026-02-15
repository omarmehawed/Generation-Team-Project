<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('attendances', function (Blueprint $table) {
        $table->id();
        // ربط الاجتماع
        $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
        
        // ربط الطالب (غالباً هو يوزر في جدول users)
        $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
        
        // الحالة: 1 حاضر، 0 غايب
        $table->boolean('status')->default(0); 
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
