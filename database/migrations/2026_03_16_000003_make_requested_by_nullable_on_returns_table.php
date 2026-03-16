<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `returns` MODIFY `requested_by` ENUM('self', 'user') NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE `returns` SET `requested_by` = 'user' WHERE `requested_by` IS NULL");
        DB::statement("ALTER TABLE `returns` MODIFY `requested_by` ENUM('self', 'user') NOT NULL DEFAULT 'user'");
    }
};
