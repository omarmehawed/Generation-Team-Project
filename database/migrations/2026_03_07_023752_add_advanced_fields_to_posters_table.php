<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posters', function (Blueprint $table) {
            $table->string('template_type')->default('standard')->after('id');
            $table->json('images')->nullable()->after('image_path');
            $table->json('links')->nullable()->after('layout_settings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posters', function (Blueprint $table) {
            $table->dropColumn(['template_type', 'images', 'links']);
        });
    }
};
