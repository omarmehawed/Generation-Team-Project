<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            // بنضيف العواميد لو مش موجودة
            if (!Schema::hasColumn('projects', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('projects', 'max_members')) {
                $table->integer('max_members')->default(5)->after('deadline');
            }
            if (!Schema::hasColumn('projects', 'max_score')) {
                $table->integer('max_score')->default(100)->after('max_members');
            }
            if (!Schema::hasColumn('projects', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('max_score');
            }
            // لو عمود الكورس مش موجود (عشان نربط المشروع بالمادة)
            if (!Schema::hasColumn('projects', 'course_id')) {
                $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade')->after('id');
            }
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['description', 'max_members', 'max_score', 'is_active', 'course_id']);
        });
    }
};
