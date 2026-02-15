<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. لو العمود القديم موجود، امسحه (نضافة)
            if (Schema::hasColumn('users', 'last_editor_id')) {
                // تأكد إنك ماسح الـ foreign key لو موجود (اختياري حسب داتا بيزك)
                // $table->dropForeign(['last_editor_id']);
                $table->dropColumn('last_editor_id');
            }

            // 2. ضيف العمود الجديد (لو مش موجود)
            if (!Schema::hasColumn('users', 'created_by_id')) {
                $table->unsignedBigInteger('created_by_id')->nullable()->after('remember_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
