<?php

namespace App\Models;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Products extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'product_code',
        'category_id',
        'collection_id',
        'name',
        'slug',
        'description',
        'base_price',
        'main_image_url',
        'gallery_image_urls',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'gallery_image_urls' => 'array',
        'is_active' => 'boolean',
    ];

    // tạo slug tự động từ tên sản phẩm
    public function sluggable(): array
    {
        return
        [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collections::class, 'collection_id');
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return $query;
        }

        return $query->where(function (Builder $scope) use ($keyword): void {
            $scope->where('name', 'like', '%'.$keyword.'%')
                ->orWhere('product_code', 'like', '%'.$keyword.'%')
                ->orWhere('description', 'like', '%'.$keyword.'%')
                ->orWhereHas('category', function (Builder $subQuery) use ($keyword): void {
                    $subQuery->where('name', 'like', '%'.$keyword.'%');
                })
                ->orWhereHas('collection', function (Builder $subQuery) use ($keyword): void {
                    $subQuery->where('name', 'like', '%'.$keyword.'%');
                });
        });
    }

    public function skus(): HasMany
    {
        return $this->hasMany(ProductSkus::class, 'product_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(OrderFeedback::class, 'product_id');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'product_id');
    }

    public function flashSales(): HasMany
    {
        return $this->hasMany(FlashSale::class, 'product_id');
    }

    public function effectiveFlashSales()
    {
        $sales = $this->flashSales()->get();

        if ($this->relationLoaded('category') || $this->category) {
            $category = $this->category;
            if ($category) {
                $sales = $sales->merge($category->flashSales()->get());
                if ($category->parent) {
                    $sales = $sales->merge($category->parent->flashSales()->get());
                }
            }
        }

        $now = Carbon::now();

        $filtered = $sales->filter(function ($s) use ($now) {
            if (isset($s->is_active) && ! $s->is_active) {
                return false;
            }
            if (! empty($s->start_date) && $now->lt(Carbon::parse($s->start_date))) {
                return false;
            }
            if (! empty($s->end_date) && $now->gt(Carbon::parse($s->end_date))) {
                return false;
            }

            return true;
        })->unique('id')->values();

        return $filtered;
    }

    public function hasActiveFlashSale(): bool
    {
        return $this->effectiveFlashSales()->isNotEmpty();
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }
}
