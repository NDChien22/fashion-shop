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
        $driver = DB::getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("UPDATE payments SET status = 'pending' WHERE status NOT IN ('pending','paid','refunded','failed') OR status IS NULL");
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','paid','refunded','failed') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("UPDATE payments SET status = 'paid' WHERE status = 'refunded'");
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending'");
    }
};
