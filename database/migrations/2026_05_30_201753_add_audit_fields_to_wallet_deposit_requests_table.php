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
        Schema::table('wallet_deposit_requests', function (Blueprint $table) {
            $table->boolean('is_edited')->default(false)->after('rejection_reason');
            $table->json('old_values')->nullable()->after('is_edited');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_deposit_requests', function (Blueprint $table) {
            $table->dropColumn(['is_edited', 'old_values']);
        });
    }
};
