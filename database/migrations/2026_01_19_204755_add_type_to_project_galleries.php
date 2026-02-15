<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_galleries', function (Blueprint $table) {
            // بنزود عمود النوع (صورة ولا فيديو)
            if (!Schema::hasColumn('project_galleries', 'type')) {
                $table->string('type')->after('user_id'); // القيم المتوقعة: 'image', 'video'
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_galleries', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
