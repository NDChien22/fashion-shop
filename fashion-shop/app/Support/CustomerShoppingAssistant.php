<?php

namespace App\Support;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Products;
use App\Models\ProductSkus;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\Whistlist;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CustomerShoppingAssistant
{
    // Gợi ý sản phẩm dựa trên hành vi, lịch sử mua hàng
    public function suggestedProducts(User $user, int $limit = 3): Collection
    {
        $signals = [];

        $this->addWishlistSignals($user, $signals);
        $this->addPurchaseSignals($user, $signals);
        $this->addActivitySignals($user, $signals);

        if ($signals === []) {
            return $this->fallbackProducts($limit);
        }

        return collect($signals)
            ->sortByDesc(fn (array $signal): int => $signal['score'])
            ->take($limit)
            ->values()
            ->map(function (array $signal): array {
                $product = $signal['product'];

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image_url' => $product->main_image_url,
                    'price' => $this->displayPrice($product),
                    'reason' => implode('; ', $signal['reasons']),
                    'url' => route('user.product-detail', ['product' => $product->slug]),
                ];
            });
    }

    // thu thập tín hiệu từ các sản phẩm đã lưu trong wishlist
    private function addWishlistSignals(User $user, array &$signals): void
    {
        Whistlist::query()
            ->with(['product.category:id,name', 'product.collection:id,name'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(6)
            ->get()
            ->each(function (Whistlist $item) use (&$signals): void {
                $product = $item->product;

                if (! $product) {
                    return;
                }

                $this->pushSignal($signals, $product, 120, 'đã lưu trong wishlist');
            });
    }

    // Thu thập tín hiệu từ các đơn hàng đã xử lý hoặc hoàn thành
    private function addPurchaseSignals(User $user, array &$signals): void
    {
        Order::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])
            ->with(['items.productSku.product.category', 'items.productSku.product.collection'])
            ->latest('id')
            ->limit(4)
            ->get()
            ->each(function (Order $order) use (&$signals): void {
                $order->items->each(function ($item) use (&$signals): void {
                    $product = $item->productSku?->product;

                    if (! $product) {
                        return;
                    }

                    $this->pushSignal($signals, $product, 160, 'đã mua gần đây');
                });
            });
    }

    // Thu thập tín hiệu từ lịch sử tương tác gần đây của người dùng.
    private function addActivitySignals(User $user, array &$signals): void
    {
        UserActivity::query()
            ->where('user_id', $user->id)
            ->whereIn('action', ['view', 'click', 'add_to_cart', 'search'])
            ->latest('id')
            ->limit(12)
            ->get()
            ->each(function (UserActivity $activity) use (&$signals): void {
                $this->addActivityProductSignals($activity, $signals);
                $this->addSearchTermSignals($activity, $signals);
            });
    }

    // Gắn điểm gợi ý cho sản phẩm từ một hoạt động cụ thể.
    private function addActivityProductSignals(UserActivity $activity, array &$signals): void
    {
        $action = $activity->action ?? 'view';

        if (! is_string($activity->target_type) || ! is_numeric($activity->target_id)) {
            return;
        }

        $targetType = strtolower($activity->target_type);
        $targetId = (int) $activity->target_id;

        if (str_contains($targetType, 'sku')) {
            $sku = ProductSkus::query()->with(['product.category:id,name', 'product.collection:id,name'])->find($targetId);
            $product = $sku?->product;

            if ($product) {
                $this->pushSignal($signals, $product, $this->activityScore($action), 'đã xem/ thao tác gần đây');
            }

            return;
        }

        if (! str_contains($targetType, 'product')) {
            return;
        }

        $product = Products::query()
            ->with(['category:id,name', 'collection:id,name'])
            ->find($targetId);

        if (! $product) {
            return;
        }

        $this->pushSignal($signals, $product, $this->activityScore($action), 'đã xem gần đây');
    }

    // Tìm sản phẩm khớp với từ khóa tìm kiếm gần nhất
    private function addSearchTermSignals(UserActivity $activity, array &$signals): void
    {
        $term = $this->extractSearchTerm($activity);

        if ($term === null) {
            return;
        }

        Products::query()
            ->with(['category:id,name', 'collection:id,name'])
            ->where('is_active', true)
            ->where(function ($query) use ($term): void {
                $query->where('name', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%');
            })
            ->orderByDesc('id')
            ->limit(2)
            ->get()
            ->each(function (Products $product) use (&$signals, $term): void {
                $this->pushSignal($signals, $product, 70, 'phù hợp với tìm kiếm "'.$this->compactText($term).'"');
            });
    }

    /**
     * Lưu hoặc cộng dồn điểm gợi ý cho một sản phẩm.
     */
    private function pushSignal(array &$signals, Products $product, int $score, string $reason): void
    {
        $productId = (int) $product->id;

        if (! isset($signals[$productId])) {
            $signals[$productId] = [
                'product' => $product,
                'score' => 0,
                'reasons' => [],
            ];
        }

        $signals[$productId]['score'] += $score;
        $signals[$productId]['reasons'][] = $reason;
        $signals[$productId]['reasons'] = array_values(array_unique($signals[$productId]['reasons']));
    }

    /**
     * Trả về danh sách sản phẩm dự phòng khi không có đủ tín hiệu.
     */
    private function fallbackProducts(int $limit): Collection
    {
        return Products::query()
            ->with(['category:id,name', 'collection:id,name'])
            ->where('is_active', true)
            ->whereNotNull('main_image_url')
            ->where('main_image_url', '!=', '')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function (Products $product): array {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image_url' => $product->main_image_url,
                    'price' => $this->displayPrice($product),
                    'reason' => 'Sản phẩm nổi bật hiện tại',
                    'url' => route('user.product-detail', ['product' => $product->slug]),
                ];
            });
    }

    // Chấm điểm theo loại hành vi người dùng.
    private function activityScore(string $action): int
    {
        return match ($action) {
            'add_to_cart' => 110,
            'click' => 90,
            'view' => 70,
            'search' => 50,
            default => 60,
        };
    }

    // Trích xuất từ khóa tìm kiếm từ metadata của hoạt động.
    private function extractSearchTerm(UserActivity $activity): ?string
    {
        if ($activity->action !== 'search') {
            return null;
        }

        if (! is_array($activity->metadata)) {
            return null;
        }

        foreach (['query', 'keyword', 'search', 'term'] as $key) {
            $value = $activity->metadata[$key] ?? null;

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        foreach ($activity->metadata as $value) {
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }

    private function normalizeSearchText(string $value): string
    {
        return trim(Str::ascii(mb_strtolower($this->compactText($value))));
    }

    private function compactText(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }

    private function displayPrice(Products $product): float
    {
        $flashSales = FlashSalePricing::activeSales();
        $product = FlashSalePricing::applyProduct($product, $flashSales);

        return FlashSalePricing::displayPrice($product);
    }
}
