<?php

namespace App\Providers;

use App\Models\Whistlist;
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
            $query = Whistlist::query();

            if (Auth::check()) {
                $query->where('user_id', (int) Auth::id());
            } else {
                $request = request();

                if (! $request || ! $request->hasSession()) {
                    $view->with('globalWhistlistProductIds', []);
                    $view->with('globalWhistlistCount', 0);

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

            $view->with('globalWhistlistProductIds', $ids);
            $view->with('globalWhistlistCount', count($ids));
        });
    }
}
