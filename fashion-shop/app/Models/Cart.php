<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_sku_id',
        'quantity',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'product_sku_id' => 'integer',
        'quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function productSku(): BelongsTo
    {
        return $this->belongsTo(ProductSkus::class, 'product_sku_id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession(Builder $query, string $sessionId): Builder
    {
        return $query->where('session_id', $sessionId);
    }

    public static function putForUser(int $userId, int $productSkuId, int $quantity = 1): self
    {
        $item = static::firstOrNew([
            'user_id' => $userId,
            'product_sku_id' => $productSkuId,
        ]);

        $item->quantity = max(1, ($item->exists ? $item->quantity : 0) + $quantity);
        $item->session_id = null;
        $item->save();

        return $item;
    }

    public static function putForSession(string $sessionId, int $productSkuId, int $quantity = 1): self
    {
        $item = static::firstOrNew([
            'session_id' => $sessionId,
            'product_sku_id' => $productSkuId,
            'user_id' => null,
        ]);

        $item->quantity = max(1, ($item->exists ? $item->quantity : 0) + $quantity);
        $item->save();

        return $item;
    }

    public static function mergeSessionToUser(string $sessionId, int $userId): void
    {
        static::query()
            ->forSession($sessionId)
            ->get()
            ->each(function (self $sessionItem) use ($userId): void {
                $userItem = static::firstOrNew([
                    'user_id' => $userId,
                    'product_sku_id' => $sessionItem->product_sku_id,
                ]);

                $userItem->quantity = ($userItem->exists ? $userItem->quantity : 0) + $sessionItem->quantity;
                $userItem->session_id = null;
                $userItem->save();

                $sessionItem->delete();
            });
    }

    public function getLineAmountAttribute(): float
    {
        $basePrice = (float) ($this->productSku?->product?->base_price ?? 0);

        return round($basePrice * $this->quantity, 2);
    }
}
