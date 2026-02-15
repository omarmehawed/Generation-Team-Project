<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    // الشرط: لو الجدول مش موجود... أنشئه
    if (!Schema::hasTable('fund_contributions')) {
        
        Schema::create('fund_contributions', function (Blueprint $table) {
            $table->id();
            
            // ربط المساهمة بجدول التمويل (Funds)
            // لاحظ: لازم الاسم هنا يطابق اسم الجدول الأب (project_funds) مفرد + _id
            $table->foreignId('project_fund_id')->constrained('project_funds')->onDelete('cascade');
            
            // ربط المساهمة بالطالب اللي دفع
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->decimal('amount', 10, 2); // المبلغ المدفوع
            $table->timestamps();
        });
    }
}
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_contributions');
    }
};
