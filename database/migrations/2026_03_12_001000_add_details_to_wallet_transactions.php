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
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('balance_after');
            $table->foreignId('deposit_request_id')->nullable()->after('notes')->constrained('wallet_deposit_requests')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropForeign(['deposit_request_id']);
            $table->dropColumn(['notes', 'deposit_request_id']);
        });
    }
};
