<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->unsignedInteger('extra_time_minutes')->default(0)->after('ends_at');
            $table->timestamp('extra_time_ends_at')->nullable()->after('extra_time_minutes');
            $table->foreignId('extra_time_granted_by')->nullable()->constrained('users')->nullOnDelete()->after('extra_time_ends_at');
            $table->timestamp('extra_time_granted_at')->nullable()->after('extra_time_granted_by');
            $table->text('extra_time_notes')->nullable()->after('extra_time_granted_at');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropForeign(['extra_time_granted_by']);
            $table->dropColumn([
                'extra_time_minutes',
                'extra_time_ends_at',
                'extra_time_granted_by',
                'extra_time_granted_at',
                'extra_time_notes',
            ]);
        });
    }
};
