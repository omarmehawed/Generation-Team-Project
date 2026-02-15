<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            // 1. عمود المبلغ (عملناه decimal عشان يقبل قروش لو احتجت)
            if (!Schema::hasColumn('project_expenses', 'amount')) {
                $table->decimal('amount', 10, 2)->after('shop_name');
            }

            // 2. عمود مسار الصورة (عشان تخزين الفاتورة)
            if (!Schema::hasColumn('project_expenses', 'receipt_path')) {
                $table->string('receipt_path')->nullable()->after('amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropColumn(['amount', 'receipt_path']);
        });
    }
};
