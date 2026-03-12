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
        // Update wallet_transactions table
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->decimal('balance_after', 10, 2)->after('amount')->nullable();
        });

        // Create wallet_deposit_requests table
        Schema::create('wallet_deposit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // cash, vodafone_cash, instapay
            
            // For Cash
            $table->text('notes')->nullable();
            
            // For Vodafone Cash / InstaPay
            $table->string('phone_number')->nullable();
            $table->date('transfer_date')->nullable();
            $table->time('transfer_time')->nullable();
            $table->string('screenshot_path')->nullable();
            
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_deposit_requests');
        
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn('balance_after');
        });
    }
};
