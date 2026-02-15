<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    // جدول طلبات التمويل (مثلاً: طلب شراء ماتور)
    Schema::create('project_funds', function (Blueprint $table) {
        $table->id();
        $table->foreignId('team_id')->constrained()->onDelete('cascade');
        $table->string('title'); // عنوان الطلب (شراء كذا)
        $table->decimal('amount_per_member', 10, 2); // المطلوب من الفرد الواحد
        $table->date('deadline'); // آخر ميعاد للدفع
        $table->timestamps();
    });

    // جدول متابعة دفع الأعضاء
    Schema::create('fund_contributions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('fund_id')->constrained('project_funds')->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
        $table->timestamp('paid_at')->nullable(); // دفع امتى
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_funds_tables');
    }
};
