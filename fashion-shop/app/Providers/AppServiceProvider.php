<?php

namespace App\Providers;

use App\Enums\OrderReturnRequestStatus;
use App\Models\OrderReturnRequest;
use App\Models\Products;
use App\Models\Wishlist;
use App\Support\FlashSalePricing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view): void {
            $query = Wishlist::query();

            if (Auth::check()) {
                $query->where('user_id', (int) Auth::id());
            } else {
                $request = request();

                if (! $request || ! $request->hasSession()) {
                    $view->with('globalWishlistProductIds', []);
                    $view->with('globalWishlistCount', 0);

                    return;
                }

                $query->whereNull('user_id')
                    ->where('session_id', $request->session()->getId());
            }

            $ids = $query
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            $view->with('globalWishlistProductIds', $ids);
            $view->with('globalWishlistCount', count($ids));
            $view->with(
                'globalReturnRequestsPendingCount',
                (int) OrderReturnRequest::query()->where('status', OrderReturnRequestStatus::PENDING->value)->count()
            );

            // Áp dụng flash sale pricing cho tất cả biến view chứa Products (model) hoặc Collection của Products
            $data = $view->getData();

            foreach ($data as $key => $value) {
                try {
                    // Collection of products
                    if ($value instanceof Collection) {
                        if ($value->isEmpty()) {
                            continue;
                        }

                        $first = $value->first();
                        if ($first instanceof Products) {
                            $mapped = $value->map(function ($p) {
                                if ($p instanceof Products) {
                                    return FlashSalePricing::applyProduct($p);
                                }

                                return $p;
                            });

                            $view->with($key, $mapped);
                        }

                        continue;
                    }

                    // Single product model
                    if ($value instanceof Products) {
                        $applied = FlashSalePricing::applyProduct($value);
                        $view->with($key, $applied);
                    }
                } catch (\Throwable $e) {
                    // ignore failures to avoid breaking view rendering
                }
            }
        });
    }
}
