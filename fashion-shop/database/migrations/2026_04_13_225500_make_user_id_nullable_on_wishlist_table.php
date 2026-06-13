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

        if (! Schema::hasTable('wishlists')) {
            return;
        }

        // Ensure guest wishlist rows can be stored with user_id = NULL.
        try {
            DB::statement('ALTER TABLE `wishlists` DROP FOREIGN KEY `wishlists_user_id_foreign`');
        } catch (Throwable $e) {
            // Foreign key might not exist yet.
        }

        DB::statement('ALTER TABLE `wishlists` MODIFY `user_id` BIGINT UNSIGNED NULL');

        DB::statement('ALTER TABLE `wishlists` ADD CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL');
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        if (! Schema::hasTable('wishlists')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE `wishlists` DROP FOREIGN KEY `wishlists_user_id_foreign`');
        } catch (Throwable $e) {
            // Ignore when foreign key is already absent.
        }

        DB::statement('ALTER TABLE `wishlists` MODIFY `user_id` BIGINT UNSIGNED NOT NULL');

        DB::statement('ALTER TABLE `wishlists` ADD CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE');
    }
};
