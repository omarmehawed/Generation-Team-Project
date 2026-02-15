<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            
            // 1. وقت التصحيح/المراجعة
            if (!Schema::hasColumn('tasks', 'graded_at')) {
                $table->timestamp('graded_at')->nullable()->after('submitted_at');
            }

            // 2. مين اللي صحح/راجع (الليدر أو النائب)
            if (!Schema::hasColumn('tasks', 'graded_by')) {
                $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null')->after('graded_at');
            }

            // 3. الدرجة (لو التاسك عليه درجة)
            if (!Schema::hasColumn('tasks', 'grade')) {
                $table->decimal('grade', 5, 2)->nullable()->after('submission_value');
            }

            // 4. ملاحظات التصحيح (Feedback)
            if (!Schema::hasColumn('tasks', 'feedback')) {
                $table->text('feedback')->nullable()->after('grade');
            }
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['graded_by']);
            $table->dropColumn(['graded_at', 'graded_by', 'grade', 'feedback']);
        });
    }
};