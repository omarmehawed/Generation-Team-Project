<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('expense_contributions', function (Blueprint $table) {
        $table->id();
        // Foreign key to the expense (fund request)
        $table->foreignId('project_expense_id')->constrained('project_expenses')->onDelete('cascade');
        // Foreign key to the user (student)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        // Status of contribution
        $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_contributions');
    }
};
