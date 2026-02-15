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
        Schema::table('join_requests', function (Blueprint $table) {
            $table->string('home_address')->nullable()->after('whatsapp_number');
            $table->boolean('is_dorm')->default(false)->after('home_address');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('home_address')->nullable()->after('phone_number');
            $table->boolean('is_dorm')->default(false)->after('home_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('join_requests', function (Blueprint $table) {
            $table->dropColumn(['home_address', 'is_dorm']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['home_address', 'is_dorm']);
        });
    }
};
