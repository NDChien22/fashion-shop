<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->after('payment_method');
            }

            if (! Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_name');
            }

            if (! Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone')->after('customer_email');
            }

            if (! Schema::hasColumn('orders', 'shipping_address')) {
                $table->text('shipping_address')->after('customer_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = [];
            foreach (['customer_name', 'customer_email', 'customer_phone', 'shipping_address'] as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $cols[] = $col;
                }
            }

            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
