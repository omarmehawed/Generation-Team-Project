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
        Schema::table('fund_contributions', function (Blueprint $table) {
            $table->date('transaction_date')->nullable();
            $table->time('transaction_time')->nullable();
            $table->string('from_number')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fund_contributions', function (Blueprint $table) {
            $table->dropColumn(['transaction_date', 'transaction_time', 'from_number', 'notes', 'rejection_reason']);
        });
    }
};
