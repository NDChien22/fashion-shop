<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('whistlists')) {
            return;
        }

        Schema::table('whistlists', function (Blueprint $table): void {
            if (! Schema::hasColumn('whistlists', 'session_id')) {
                $table->string('session_id', 100)->nullable()->after('user_id');
            }
        });

        try {
            Schema::table('whistlists', function (Blueprint $table): void {
                $table->unique(['session_id', 'product_id'], 'whistlists_session_id_product_id_unique');
            });
        } catch (Throwable $e) {
            // Ignore if index already exists.
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('whistlists')) {
            return;
        }

        try {
            Schema::table('whistlists', function (Blueprint $table): void {
                $table->dropUnique('whistlists_session_id_product_id_unique');
            });
        } catch (Throwable $e) {
            // Ignore if index does not exist.
        }

        if (Schema::hasColumn('whistlists', 'session_id')) {
            Schema::table('whistlists', function (Blueprint $table): void {
                $table->dropColumn('session_id');
            });
        }
    }
};
