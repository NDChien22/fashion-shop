<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $legacyTable = 'whist'.'lists';
        $newTable = 'wishlists';

        if (Schema::hasTable($legacyTable) && ! Schema::hasTable($newTable)) {
            Schema::rename($legacyTable, $newTable);
        }
    }

    public function down(): void
    {
        $legacyTable = 'whist'.'lists';
        $newTable = 'wishlists';

        if (Schema::hasTable($newTable) && ! Schema::hasTable($legacyTable)) {
            Schema::rename($newTable, $legacyTable);
        }
    }
};
