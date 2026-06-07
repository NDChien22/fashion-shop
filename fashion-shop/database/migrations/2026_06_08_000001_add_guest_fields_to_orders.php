<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'guest_name')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('guest_name')->nullable()->after('payment_method');
            });
        }

        if (! Schema::hasColumn('orders', 'guest_email')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('guest_email')->nullable()->after('guest_name');
            });
        }

        if (! Schema::hasColumn('orders', 'guest_phone')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('guest_phone')->nullable()->after('guest_email');
            });
        }

        if (! Schema::hasColumn('orders', 'guest_address')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->text('guest_address')->nullable()->after('guest_phone');
            });
        }

        // Copy existing customer_* into guest_* for orders created by guests (no user_id)
        if (Schema::hasColumn('orders', 'customer_name')) {
            DB::table('orders')
                ->whereNull('user_id')
                ->whereNotNull('customer_name')
                ->update(['guest_name' => DB::raw('customer_name')]);
        }

        if (Schema::hasColumn('orders', 'customer_email')) {
            DB::table('orders')
                ->whereNull('user_id')
                ->whereNotNull('customer_email')
                ->update(['guest_email' => DB::raw('customer_email')]);
        }

        if (Schema::hasColumn('orders', 'customer_phone')) {
            DB::table('orders')
                ->whereNull('user_id')
                ->whereNotNull('customer_phone')
                ->update(['guest_phone' => DB::raw('customer_phone')]);
        }

        if (Schema::hasColumn('orders', 'shipping_address')) {
            DB::table('orders')
                ->whereNull('user_id')
                ->whereNotNull('shipping_address')
                ->update(['guest_address' => DB::raw('shipping_address')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'guest_name')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('guest_name');
            });
        }

        if (Schema::hasColumn('orders', 'guest_email')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('guest_email');
            });
        }

        if (Schema::hasColumn('orders', 'guest_phone')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('guest_phone');
            });
        }

        if (Schema::hasColumn('orders', 'guest_address')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('guest_address');
            });
        }
    }
};
