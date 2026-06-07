<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql' && DB::getDriverName() !== 'mariadb') {
            return;
        }
        Schema::table('order_vouchers', function (Blueprint $table): void {
            $table->unsignedBigInteger('order_id')->after('id');
            $table->unsignedBigInteger('voucher_id')->after('order_id');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('voucher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql' && DB::getDriverName() !== 'mariadb') {
            return;
        }
        Schema::table('order_vouchers', function (Blueprint $table): void {
            $table->dropColumn(['order_id', 'voucher_id', 'discount_amount']);
        });
    }
};
