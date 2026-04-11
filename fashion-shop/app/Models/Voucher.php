<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'category',
        'category_id',
        'collection_id',
        'product_id',
        'discount_type',
        'discount_value',
        'min_order_value',
        'max_discount',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'is_active'
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
