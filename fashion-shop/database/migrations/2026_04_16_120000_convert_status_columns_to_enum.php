<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("UPDATE orders SET status = 'pending' WHERE status NOT IN ('pending','processing','completed','cancelled','payment_failed') OR status IS NULL");
        DB::statement("UPDATE orders SET shipping_status = 'pending' WHERE shipping_status NOT IN ('pending','shipping','delivered','cancelled') OR shipping_status IS NULL");
        DB::statement("UPDATE payments SET status = 'pending' WHERE status NOT IN ('pending','paid','failed') OR status IS NULL");
        DB::statement("UPDATE user_vouchers SET status = 'unused' WHERE status NOT IN ('unused','used','expired') OR status IS NULL");

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','processing','completed','cancelled','payment_failed') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders MODIFY COLUMN shipping_status ENUM('pending','shipping','delivered','cancelled') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE user_vouchers MODIFY COLUMN status ENUM('unused','used','expired') NOT NULL DEFAULT 'unused'");
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders MODIFY COLUMN shipping_status VARCHAR(255) NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE payments MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE user_vouchers MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'unused'");
    }
};
