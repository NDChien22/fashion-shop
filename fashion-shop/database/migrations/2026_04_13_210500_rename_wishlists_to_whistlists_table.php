<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wishlists') && ! Schema::hasTable('whistlists')) {
            Schema::rename('wishlists', 'whistlists');
        }

        if (! Schema::hasTable('whistlists')) {
            return;
        }

        Schema::table('whistlists', function (Blueprint $table): void {
            try {
                $table->unique(['user_id', 'product_id'], 'whistlists_user_id_product_id_unique');
            } catch (Throwable $e) {
                // Ignore when unique index already exists.
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('whistlists') && ! Schema::hasTable('wishlists')) {
            Schema::rename('whistlists', 'wishlists');
        }
    }
};
