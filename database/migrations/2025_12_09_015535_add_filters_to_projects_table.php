<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('projects', function (Blueprint $table) {
        
        // 1. نوع المشروع (مادة ولا تخرج)
        if (!Schema::hasColumn('projects', 'project_type')) {
            $table->enum('project_type', ['subject', 'graduation'])->default('subject'); 
        }

        // 2. السنة الدراسية (من 1 لـ 4)
        if (!Schema::hasColumn('projects', 'academic_year')) {
            $table->enum('academic_year', ['1', '2', '3', '4'])->nullable();
        }
    });
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
