<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProjectTypeToString extends Migration
{
    public function up()
    {
        // تعديل العمود لنص عشان يقبل graduation
        Schema::table('projects', function (Blueprint $table) {
            // لارافل هنا هيعرف إنك شغال على PostgreSQL وهيحول الكلمة لـ ALTER COLUMN لوحده
            $table->string('type')->default('subject')->change();
        });
    }

    public function down()
    {
        // التراجع (اختياري)
        DB::statement("ALTER TABLE projects MODIFY COLUMN type ENUM('subject') NOT NULL DEFAULT 'subject'");
    }
}
