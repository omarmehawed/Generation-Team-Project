<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('project_expenses', 'component_id')) {
                $table->foreignId('component_id')->nullable()->after('team_id')->constrained('project_components')->onDelete('set null');
            }
            if (!Schema::hasColumn('project_expenses', 'price_per_unit')) {
                $table->decimal('price_per_unit', 10, 2)->nullable()->after('component_id');
            }
            if (!Schema::hasColumn('project_expenses', 'quantity')) {
                $table->unsignedInteger('quantity')->default(1)->after('price_per_unit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropForeign(['component_id']);
            $table->dropColumn(['component_id', 'price_per_unit', 'quantity']);
        });
    }
};
