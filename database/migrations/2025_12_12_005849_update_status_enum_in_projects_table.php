<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    public function up()
{
    // هنا بنعدل عمود الـ status وبنضيفله كلمة 'completed'
    // لاحظ: لازم نكتب كل الحالات القديمة تاني عشان ميمسحهاش
    DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('pending', 'pending_review', 'in_progress', 'ready_for_defense', 'completed', 'rejected') DEFAULT 'pending'");
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            //
        });
    }
};
