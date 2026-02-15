<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            // بنزود عمود اسم المنتج
            if (!Schema::hasColumn('project_expenses', 'item')) {
                $table->string('item')->after('user_id');
            }

            // بنزود عمود اسم المحل (احتياطي لو مش موجود)
            if (!Schema::hasColumn('project_expenses', 'shop_name')) {
                $table->string('shop_name')->nullable()->after('item');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropColumn(['item', 'shop_name']);
        });
    }
};
