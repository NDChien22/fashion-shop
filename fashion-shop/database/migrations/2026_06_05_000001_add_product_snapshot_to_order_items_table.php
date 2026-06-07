<?php

use App\Models\OrderItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->string('product_name')->nullable()->after('product_sku_id');
            $table->string('product_sku')->nullable()->after('product_name');
            $table->string('product_size')->nullable()->after('product_sku');
            $table->string('product_color')->nullable()->after('product_size');
        });

        OrderItem::query()
            ->with(['productSku.product'])
            ->chunkById(500, function ($items): void {
                foreach ($items as $item) {
                    $item->forceFill([
                        'product_name' => (string) data_get($item, 'productSku.product.name', $item->product_name),
                        'product_sku' => (string) data_get($item, 'productSku.sku', $item->product_sku),
                        'product_size' => (string) data_get($item, 'productSku.size', $item->product_size),
                        'product_color' => (string) data_get($item, 'productSku.color', $item->product_color),
                    ])->save();
                }
            });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn([
                'product_name',
                'product_sku',
                'product_size',
                'product_color',
            ]);
        });
    }
};
