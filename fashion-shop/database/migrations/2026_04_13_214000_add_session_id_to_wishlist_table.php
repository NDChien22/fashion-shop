<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('wishlists')) {
            return;
        }

        Schema::table('wishlists', function (Blueprint $table): void {
            if (! Schema::hasColumn('wishlists', 'session_id')) {
                $table->string('session_id', 100)->nullable()->after('user_id');
            }
        });

        try {
            Schema::table('wishlists', function (Blueprint $table): void {
                $table->unique(['session_id', 'product_id'], 'wishlists_session_id_product_id_unique');
            });
        } catch (Throwable $e) {
            // Ignore if index already exists.
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('wishlists')) {
            return;
        }

        try {
            Schema::table('wishlists', function (Blueprint $table): void {
                $table->dropUnique('wishlists_session_id_product_id_unique');
            });
        } catch (Throwable $e) {
            // Ignore if index does not exist.
        }

        if (Schema::hasColumn('wishlists', 'session_id')) {
            Schema::table('wishlists', function (Blueprint $table): void {
                $table->dropColumn('session_id');
            });
        }
    }
};
