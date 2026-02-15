<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import DB
return new class extends Migration
{
    public function up()
{
    // Modify the column to include 'rejected'
    // Adjust 'pending' and 'confirmed' if your existing values are different
    DB::statement("ALTER TABLE meetings MODIFY COLUMN status ENUM('pending', 'confirmed', 'rejected') DEFAULT 'pending'");
}

public function down()
{
    // Revert back if needed (optional)
    DB::statement("ALTER TABLE meetings MODIFY COLUMN status ENUM('pending', 'confirmed') DEFAULT 'pending'");
}
};
