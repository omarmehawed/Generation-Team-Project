<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('project_expenses', function (Blueprint $table) {

            // إضافة shop_name لو مش موجود
            if (!Schema::hasColumn('project_expenses', 'shop_name')) {
                $table->string('shop_name')->nullable(); // شلنا after('item')
            }

            // إضافة receipt_image لو مش موجود
            if (!Schema::hasColumn('project_expenses', 'receipt_image')) {
                $table->string('receipt_image')->nullable(); // شلنا after('amount')
            }
        });
    }

    public function down()
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('project_expenses', 'shop_name')) {
                $table->dropColumn('shop_name');
            }
            if (Schema::hasColumn('project_expenses', 'receipt_image')) {
                $table->dropColumn('receipt_image');
            }
        });
    }
};
