<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
//    public function up()
// {
//     Schema::create('project_funds', function (Blueprint $table) {
//         $table->id();
        
//         // ربطنا الفلوس بالمشروع
//         $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
        
//         // بيانات المعاملة المالية
//         $table->string('item_name'); // اسم الحاجة (مثلاً: Arduino Uno)
//         $table->decimal('amount', 10, 2); // المبلغ (مثلاً: 150.00)
//         $table->string('type')->default('expense'); // نوعها (مصروف expense / إيداع deposit)
//         $table->date('transaction_date')->nullable(); // تاريخ الصرف
//         $table->string('status')->default('pending'); // حالتها (pending, approved)
//         $table->text('notes')->nullable(); // ملاحظات
        
//         $table->timestamps();
//     });
// }

    // /**
    //  * Reverse the migrations.
    //  */
    // public function down(): void
    // {
    //     Schema::dropIfExists('project_funds');
    // }
};
