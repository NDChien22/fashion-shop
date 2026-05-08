<?php

namespace App\Livewire\User;

use App\Models\Banner;
use App\Models\Collections;
use App\Models\FlashSale;
use App\Models\Products;
use App\Models\UserVoucher;
use App\Models\Voucher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class HomePage extends Component
{
    public $banners;

    public $activeFlashSales;

    public $flashSaleProducts;

    public $featuredCollections;

    public $bestSellerProducts;

    public function mount()
    {
        try {
            $now = now();
            $today = $now->toDateString();

            // Get Banners
            $this->banners = Banner::query()
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

            // Get Active Flash Sales
            $flashSales = FlashSale::query()
                ->where('is_active', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->orderByDesc('discount_value')
                ->orderByDesc('id')
                ->get();

            $this->activeFlashSales = $flashSales;

            // Get Flash Sale Products
            $this->flashSaleProducts = $this->getFlashSaleProducts(collect($flashSales));

            // Get Featured Collections
            $this->featuredCollections = Collections::query()
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

            // Get Best Seller Products
            $this->bestSellerProducts = $this->getBestSellerProducts(collect($flashSales));
        } catch (\Exception $e) {
            Log::error('HomePage mount error: '.$e->getMessage());
            // Set default values if something goes wrong
            $this->banners = collect();
            $this->activeFlashSales = collect();
            $this->flashSaleProducts = collect();
            $this->featuredCollections = collect();
            $this->bestSellerProducts = collect();
        }
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

        if (! $hasAllScopeSale) {
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
            ->map(fn (Products $product) => $this->applyBestFlashSalePrice($product, collect($flashSales)))
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
            ->leftJoinSub($soldQuantitySubQuery, 'sold_products', function ($join): void {
                $join->on('products.id', '=', 'sold_products.product_id');
            })
            ->where('products.is_active', true)
            ->whereNotNull('products.main_image_url')
            ->where('products.main_image_url', '!=', '')
            ->with(['category:id,name,slug', 'collection:id,name,slug'])
            ->orderByDesc(DB::raw('COALESCE(sold_products.sold_qty, 0)'))
            ->orderByDesc('products.id')
            ->limit(8)
            ->get([
                'products.*',
                DB::raw('COALESCE(sold_products.sold_qty, 0) as sold_qty'),
            ]);

        return $products
            ->map(fn (Products $product) => $this->applyBestFlashSalePrice($product, $flashSales))
            ->values();
    }

    private function applyBestFlashSalePrice(Products $product, Collection $flashSales): Products
    {
        $bestDiscount = 0;
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
            $discountType = $sale->discount_type;

            if ($discountType === 'percent') {
                if ($discountValue > $bestDiscount) {
                    $bestDiscount = $discountValue;
                    $bestDiscountType = 'percent';
                }
            } elseif ($discountType === 'fixed' && $bestDiscountType !== 'percent') {
                if ($discountValue > $bestDiscount) {
                    $bestDiscount = $discountValue;
                    $bestDiscountType = 'fixed';
                }
            }
        }

        $salePrice = match ($bestDiscountType) {
            'percent' => (float) $product->base_price * (1 - $bestDiscount / 100),
            'fixed' => max(0, (float) $product->base_price - $bestDiscount),
            default => null,
        };

        $product->setAttribute('sale_price', $salePrice);

        return $product;
    }

    public function saveVoucher(int $voucherId): void
    {
        if (! Auth::check()) {
            return;
        }

        $voucher = Voucher::query()->find($voucherId);

        if (! $voucher) {
            $this->dispatch('app-toast', message: 'Không tìm thấy voucher.', type: 'error');

            return;
        }

        if (
            ! $voucher->is_active
            || ($voucher->start_date && now()->lt($voucher->start_date))
            || ($voucher->end_date && now()->gt($voucher->end_date))
            || (! is_null($voucher->usage_limit) && (int) $voucher->used_count >= (int) $voucher->usage_limit)
        ) {
            $this->dispatch('app-toast', message: 'Voucher hiện không khả dụng.', type: 'error');

            return;
        }

        $userVoucher = UserVoucher::query()->firstOrCreate(
            [
                'user_id' => Auth::id(),
                'voucher_id' => $voucher->id,
            ],
            [
                'status' => 'unused',
                'collected_at' => now(),
            ]
        );

        $voucherCount = UserVoucher::query()
            ->where('user_id', Auth::id())
            ->where('status', 'unused')
            ->count();

        $this->dispatch('voucher-count-updated', count: $voucherCount);

        if ($userVoucher->wasRecentlyCreated) {
            $this->dispatch('app-toast', message: 'Đã lưu voucher vào tài khoản của bạn.', type: 'success');

            return;
        }

        $this->dispatch('app-toast', message: 'Voucher đã có trong ví của bạn.', type: 'success');
    }

    public function render()
    {
        return view('livewire.user.home-page');
    }
}
