<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_galleries', function (Blueprint $table) {
            // 1. عمود رابط الفيديو
            if (!Schema::hasColumn('project_galleries', 'video_link')) {
                $table->string('video_link')->nullable()->after('file_path');
            }

            // 2. عمود الوصف (Caption) - احتياطي عشان ميعطلناش
            if (!Schema::hasColumn('project_galleries', 'caption')) {
                $table->text('caption')->nullable()->after('video_link');
            }

            // 3. عمود التصنيف (Category) - احتياطي برضه
            if (!Schema::hasColumn('project_galleries', 'category')) {
                $table->string('category')->default('general')->after('caption');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_galleries', function (Blueprint $table) {
            $table->dropColumn(['video_link', 'caption', 'category']);
        });
    }
};
