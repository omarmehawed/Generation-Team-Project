<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. نمسح العمود القديم
            if (Schema::hasColumn('users', 'last_editor_id')) {
                //$table->dropForeign(['last_editor_id']); // لو فيه مفتاح أجنبي
                $table->dropColumn('last_editor_id');
            }

            // 2. نضيف العمود الجديد (لو مش موجود)
            if (!Schema::hasColumn('users', 'created_by_id')) {
                $table->unsignedBigInteger('created_by_id')->nullable()->after('remember_token');
            }
        });
    }
};
