<?php

namespace App\Support;

use App\Models\FlashSale;
use App\Models\Products;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class FlashSalePricing
{
    public static function activeSales(?CarbonInterface $now = null): Collection
    {
        $now = $now ?? now();

        return FlashSale::query()
            ->where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->orderByDesc('discount_value')
            ->orderByDesc('id')
            ->get();
    }

    public static function applyProduct(Products $product, ?Collection $flashSales = null, ?CarbonInterface $now = null): Products
    {
        $flashSales = $flashSales ?? self::activeSales($now);
        $salePrice = self::resolveSalePrice($product, $flashSales);

        $product->setAttribute('sale_price', $salePrice);
        $product->setAttribute('sale_discount_percent', self::discountPercent($product, $salePrice));

        return $product;
    }

    public static function applyProducts(Collection $products, ?Collection $flashSales = null, ?CarbonInterface $now = null): Collection
    {
        return $products->map(fn (Products $product) => self::applyProduct($product, $flashSales, $now));
    }

    public static function hasSale(Products $product): bool
    {
        $basePrice = (float) ($product->base_price ?? 0);
        $salePrice = self::displayPrice($product);

        return $salePrice > 0 && $salePrice < $basePrice;
    }

    public static function displayPrice(Products $product): float
    {
        $salePrice = $product->getAttribute('sale_price');

        if (is_numeric($salePrice) && (float) $salePrice > 0) {
            return (float) $salePrice;
        }

        return (float) ($product->base_price ?? 0);
    }

    public static function discountPercent(Products $product, ?float $salePrice = null): int
    {
        $basePrice = (float) ($product->base_price ?? 0);
        $salePrice = $salePrice ?? self::displayPrice($product);

        if ($basePrice <= 0 || $salePrice <= 0 || $salePrice >= $basePrice) {
            return 0;
        }

        return (int) round((($basePrice - $salePrice) / $basePrice) * 100);
    }

    private static function resolveSalePrice(Products $product, Collection $flashSales): ?float
    {
        $basePrice = (float) ($product->base_price ?? 0);
        $bestDiscount = 0.0;
        $bestDiscountType = null;

        foreach ($flashSales as $sale) {
            $isApplicable = match ($sale->scope) {
                'all' => true,
                'category' => $product->category_id == $sale->category_id,
                'collection' => $product->collection_id == $sale->collection_id,
                'product' => $product->id == $sale->product_id,
                default => false,
            };

            if (! $isApplicable) {
                continue;
            }

            $discountValue = (float) $sale->discount_value;
            $discountType = (string) $sale->discount_type;

            if ($discountType === 'percent') {
                if ($discountValue > $bestDiscount) {
                    $bestDiscount = $discountValue;
                    $bestDiscountType = 'percent';
                }

                continue;
            }

            if ($discountType === 'fixed' && $bestDiscountType !== 'percent' && $discountValue > $bestDiscount) {
                $bestDiscount = $discountValue;
                $bestDiscountType = 'fixed';
            }
        }

        return match ($bestDiscountType) {
            'percent' => max(0, $basePrice * (1 - $bestDiscount / 100)),
            'fixed' => max(0, $basePrice - $bestDiscount),
            default => null,
        };
    }
}
