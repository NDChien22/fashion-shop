<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        if (! Schema::hasTable('whistlists')) {
            return;
        }

        // Ensure guest whistlist rows can be stored with user_id = NULL.
        try {
            DB::statement('ALTER TABLE `whistlists` DROP FOREIGN KEY `whistlists_user_id_foreign`');
        } catch (Throwable $e) {
            // Foreign key might not exist yet.
        }

        DB::statement('ALTER TABLE `whistlists` MODIFY `user_id` BIGINT UNSIGNED NULL');

        DB::statement('ALTER TABLE `whistlists` ADD CONSTRAINT `whistlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL');
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        if (! Schema::hasTable('whistlists')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE `whistlists` DROP FOREIGN KEY `whistlists_user_id_foreign`');
        } catch (Throwable $e) {
            // Ignore when foreign key is already absent.
        }

        DB::statement('ALTER TABLE `whistlists` MODIFY `user_id` BIGINT UNSIGNED NOT NULL');

        DB::statement('ALTER TABLE `whistlists` ADD CONSTRAINT `whistlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE');
    }
};
