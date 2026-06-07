<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wishlists') && ! Schema::hasTable('whistlists')) {
            Schema::rename('wishlists', 'whistlists');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('whistlists') && ! Schema::hasTable('wishlists')) {
            Schema::rename('whistlists', 'wishlists');
        }
    }
};
