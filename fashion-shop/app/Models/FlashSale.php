<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSale extends Model
{
    protected $fillable = [
        'name',
        'discount_type',
        'discount_value',
        'scope',
        'category_id',
        'collection_id',
        'product_id',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function categoryDetail(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function collectionDetail(): BelongsTo
    {
        return $this->belongsTo(Collections::class, 'collection_id');
    }

    public function productDetail(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}