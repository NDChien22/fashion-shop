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
        DB::statement('ALTER TABLE vouchers MODIFY discount_value DECIMAL(12,2) NOT NULL');
        DB::statement('ALTER TABLE vouchers MODIFY min_order_value DECIMAL(12,2) NOT NULL');
        DB::statement('ALTER TABLE vouchers MODIFY max_discount DECIMAL(12,2) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE vouchers MODIFY discount_value DECIMAL(8,2) NOT NULL');
        DB::statement('ALTER TABLE vouchers MODIFY min_order_value DECIMAL(8,2) NOT NULL');
        DB::statement('ALTER TABLE vouchers MODIFY max_discount DECIMAL(8,2) NULL');
    }
};
