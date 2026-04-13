<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'banner_type',
        'category_id',
        'collection_id',
        'image_url',
        'is_active',
        'start_date',
        'end_date',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'category_id' => 'integer',
        'collection_id' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collections::class, 'collection_id');
    }

    public function targetTypeLabel(): string
    {
        return match ($this->banner_type) {
            'all' => 'Toàn bộ sản phẩm',
            'category' => 'Danh mục',
            'collection' => 'Bộ sưu tập',
            default => 'Không xác định',
        };
    }

    public function targetLabel(): string
    {
        return match ($this->banner_type) {
            'all' => 'Toàn bộ sản phẩm',
            'category' => $this->category?->name ?? 'Danh mục chưa chọn',
            'collection' => $this->collection?->name ?? 'Bộ sưu tập chưa chọn',
            default => 'Chưa chọn mục tiêu',
        };
    }

    public function targetUrl(): string
    {
        return match ($this->banner_type) {
            'all' => route('user.product'),
            'category' => $this->category?->slug
                ? route('user.product') . '?category=' . $this->category->slug
                : route('user.product'),
            'collection' => $this->collection?->slug
                ? route('user.collection') . '?collection=' . $this->collection->slug
                : route('user.collection'),
            default => route('user.home'),
        };
    }
}
