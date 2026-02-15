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
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade'); // ربط بالتيم
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ربط بالطالب
            
            // أنواع الأدوار: ليدر، نائب، رؤساء أقسام، وأعضاء
            $table->enum('role', [
                'leader', 'vice_leader', 
                'head_sw', 'head_hw', 
                'member_sw', 'member_hw', 
                'member'
            ])->default('member');

            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
