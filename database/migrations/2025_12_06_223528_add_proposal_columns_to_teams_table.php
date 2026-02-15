<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('teams', function (Blueprint $table) {
        $table->string('proposal_title')->nullable()->after('name');
        $table->text('proposal_description')->nullable()->after('proposal_title');
        $table->string('proposal_file')->nullable()->after('proposal_description');
        $table->text('rejection_reason')->nullable()->after('proposal_status'); // لو الدكتور رفض
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            //
        });
    }
};
