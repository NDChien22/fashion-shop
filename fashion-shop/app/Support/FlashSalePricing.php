<?php

namespace App\Support;

use App\Models\Categories;
use App\Models\FlashSale;
use App\Models\Products;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class FlashSalePricing
{
    // flashsale đang diễn ra
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

    // flashsale áp dụng cho sản phẩm
    public static function applyProduct(Products $product, ?Collection $flashSales = null, ?CarbonInterface $now = null): Products
    {
        $flashSales = $flashSales ?? self::activeSales($now);
        $salePrice = self::resolveSalePrice($product, $flashSales);

        $product->setAttribute('sale_price', $salePrice);
        $product->setAttribute('sale_discount_percent', self::discountPercent($product, $salePrice));

        return $product;
    }

    // flashsale áp dụng cho toàn bộ sản phẩm
    public static function applyProducts(Collection $products, ?Collection $flashSales = null, ?CarbonInterface $now = null): Collection
    {
        return $products->map(fn (Products $product) => self::applyProduct($product, $flashSales, $now));
    }

    // check flashsale sản phẩm
    public static function hasSale(Products $product): bool
    {
        $basePrice = (float) ($product->base_price ?? 0);
        $salePrice = self::displayPrice($product);

        return $salePrice > 0 && $salePrice < $basePrice;
    }

    // giá hiển thị sau khi áp dụng flashsale
    public static function displayPrice(Products $product): float
    {
        $salePrice = $product->getAttribute('sale_price');

        if (is_numeric($salePrice) && (float) $salePrice > 0) {
            return (float) $salePrice;
        }

        return (float) ($product->base_price ?? 0);
    }

    // tính phần trăm giảm giá từ giá gốc và giá khuyến mãi
    public static function discountPercent(Products $product, ?float $salePrice = null): int
    {
        $basePrice = (float) ($product->base_price ?? 0);
        $salePrice = $salePrice ?? self::displayPrice($product);

        if ($basePrice <= 0 || $salePrice <= 0 || $salePrice >= $basePrice) {
            return 0;
        }

        return (int) round((($basePrice - $salePrice) / $basePrice) * 100);
    }

    // tìm giá bán tốt nhất từ các flash sale áp dụng cho sản phẩm
    private static function resolveSalePrice(Products $product, Collection $flashSales): ?float
    {
        $basePrice = (float) ($product->base_price ?? 0);
        $bestDiscount = 0.0;
        $bestDiscountType = null;

        foreach ($flashSales as $sale) {
            $isApplicable = false;

            switch ($sale->scope) {
                case 'all':
                    $isApplicable = true;
                    break;
                case 'category':
                    // Check category and all ancestors up to root
                    // Ensure we have a category model with parent_id available.
                    if ($product->relationLoaded('category') && isset($product->category->parent_id)) {
                        $catModel = $product->category;
                    } else {
                        $catModel = $product->category()->select('id', 'parent_id')->first();
                    }

                    while ($catModel) {
                        if ((int) $catModel->id === (int) $sale->category_id) {
                            $isApplicable = true;
                            break;
                        }

                        if (empty($catModel->parent_id)) {
                            break;
                        }

                        $catModel = Categories::query()
                            ->select('id', 'parent_id')
                            ->find($catModel->parent_id);
                    }

                    break;
                case 'collection':
                    $isApplicable = (int) $product->collection_id === (int) $sale->collection_id;
                    break;
                case 'product':
                    $isApplicable = (int) $product->id === (int) $sale->product_id;
                    break;
                default:
                    $isApplicable = false;
            }

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
