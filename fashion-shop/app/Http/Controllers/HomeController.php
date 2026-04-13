<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Collections;
use App\Models\FlashSale;
use App\Models\Products;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $now = now();
        $today = $now->toDateString();

        $banners = Banner::query()
            ->with(['category:id,name,slug', 'collection:id,name,slug'])
            ->where('is_active', true)
            ->whereNotNull('image_url')
            ->where('image_url', '!=', '')
            ->where(function ($query) use ($today) {
                $query->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
            })
            ->where(function ($query) {
                $query->where('banner_type', 'all')
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('banner_type', 'category')->whereNotNull('category_id');
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('banner_type', 'collection')->whereNotNull('collection_id');
                    });
            })
            ->orderByDesc('id')
            ->get();

        $activeFlashSales = FlashSale::query()
            ->where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->orderByDesc('discount_value')
            ->orderByDesc('id')
            ->get();

        $flashSaleProducts = $this->getFlashSaleProducts($activeFlashSales);

        $featuredCollections = Collections::query()
            ->where('is_active', 1)
            ->withCount([
                'products' => function ($query) {
                    $query->where('is_active', true);
                },
            ])
            ->orderByDesc('products_count')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        $bestSellerProducts = $this->getBestSellerProducts($activeFlashSales);

        return view('pages.user.dashboard', [
            'banners' => $banners,
            'activeFlashSales' => $activeFlashSales,
            'flashSaleProducts' => $flashSaleProducts,
            'featuredCollections' => $featuredCollections,
            'bestSellerProducts' => $bestSellerProducts,
        ]);
    }

    private function getFlashSaleProducts(Collection $flashSales): Collection
    {
        if ($flashSales->isEmpty()) {
            return collect();
        }

        $hasAllScopeSale = $flashSales->contains(fn (FlashSale $sale) => $sale->scope === 'all');

        $productsQuery = Products::query()
            ->where('is_active', true)
            ->whereNotNull('main_image_url')
            ->where('main_image_url', '!=', '')
            ->with(['category:id,name,slug', 'collection:id,name,slug']);

        if (!$hasAllScopeSale) {
            $productsQuery->where(function ($query) use ($flashSales) {
                foreach ($flashSales as $sale) {
                    if ($sale->scope === 'category' && $sale->category_id) {
                        $query->orWhere('category_id', $sale->category_id);
                    }

                    if ($sale->scope === 'collection' && $sale->collection_id) {
                        $query->orWhere('collection_id', $sale->collection_id);
                    }

                    if ($sale->scope === 'product' && $sale->product_id) {
                        $query->orWhere('id', $sale->product_id);
                    }
                }
            });
        }

        return $productsQuery
            ->orderByDesc('id')
            ->limit(30)
            ->get()
            ->map(fn (Products $product) => $this->applyBestFlashSalePrice($product, $flashSales))
            ->filter(function (Products $product) {
                $salePrice = $product->getAttribute('sale_price');

                return is_numeric($salePrice) && (float) $salePrice < (float) $product->base_price;
            })
            ->take(10)
            ->values();
    }

    private function getBestSellerProducts(Collection $flashSales): Collection
    {
        $soldQuantitySubQuery = DB::table('order_items')
            ->join('product_skuses', 'product_skuses.id', '=', 'order_items.product_sku_id')
            ->select('product_skuses.product_id', DB::raw('SUM(order_items.quantity) as sold_qty'))
            ->groupBy('product_skuses.product_id');

        $products = Products::query()
            ->leftJoinSub($soldQuantitySubQuery, 'sold_products', function ($join) {
                $join->on('products.id', '=', 'sold_products.product_id');
            })
            ->where('products.is_active', true)
            ->whereNotNull('products.main_image_url')
            ->where('products.main_image_url', '!=', '')
            ->orderByDesc(DB::raw('COALESCE(sold_products.sold_qty, 0)'))
            ->orderByDesc('products.id')
            ->limit(10)
            ->get([
                'products.*',
                DB::raw('COALESCE(sold_products.sold_qty, 0) as sold_qty'),
            ]);

        return $products->map(fn (Products $product) => $this->applyBestFlashSalePrice($product, $flashSales));
    }

    private function isProductInFlashSaleScope(Products $product, FlashSale $flashSale): bool
    {
        return match ($flashSale->scope) {
                'all' => true,
                'category' => (int) $product->category_id === (int) $flashSale->category_id,
                'collection' => (int) $product->collection_id === (int) $flashSale->collection_id,
                'product' => (int) $product->id === (int) $flashSale->product_id,
                default => false,
            };
    }

    private function calculateSalePrice(float $basePrice, FlashSale $flashSale): float
    {
        $discountValue = (float) $flashSale->discount_value;

        $salePrice = $flashSale->discount_type === 'percent'
            ? $basePrice - (($basePrice * $discountValue) / 100)
            : $basePrice - $discountValue;

        return max($salePrice, 0);
    }

    private function applyBestFlashSalePrice(Products $product, Collection $flashSales): Products
    {
        if ($flashSales->isEmpty()) {
            $product->setAttribute('sale_price', null);
            return $product;
        }

        $basePrice = (float) $product->base_price;
        $bestSalePrice = null;

        foreach ($flashSales as $flashSale) {
            if (!$this->isProductInFlashSaleScope($product, $flashSale)) {
                continue;
            }

            $calculatedPrice = $this->calculateSalePrice($basePrice, $flashSale);

            if ($bestSalePrice === null || $calculatedPrice < $bestSalePrice) {
                $bestSalePrice = $calculatedPrice;
            }
        }

        $product->setAttribute('sale_price', $bestSalePrice);

        return $product;
    }
}