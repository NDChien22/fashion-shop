<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_feedbacks', function (Blueprint $table): void {
            if (! Schema::hasColumn('order_feedbacks', 'admin_reply_by')) {
                $table->foreignId('admin_reply_by')->nullable()->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('order_feedbacks', 'admin_reply')) {
                $table->text('admin_reply')->nullable();
            }

            if (! Schema::hasColumn('order_feedbacks', 'admin_replied_at')) {
                $table->timestamp('admin_replied_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_feedbacks', function (Blueprint $table): void {
            if (Schema::hasColumn('order_feedbacks', 'admin_reply_by')) {
                $table->dropConstrainedForeignId('admin_reply_by');
            }

            if (Schema::hasColumn('order_feedbacks', 'admin_reply')) {
                $table->dropColumn('admin_reply');
            }

            if (Schema::hasColumn('order_feedbacks', 'admin_replied_at')) {
                $table->dropColumn('admin_replied_at');
            }
        });
    }
};