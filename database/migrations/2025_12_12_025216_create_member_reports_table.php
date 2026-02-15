<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('member_reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
        $table->foreignId('reporter_id')->constrained('users'); // الطالب اللي بيشتكي
        $table->foreignId('reported_user_id')->constrained('users'); // الطالب المشكو في حقه (ليدر أو عضو)
        $table->text('complaint'); // نص الشكوى
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_reports');
    }
};
