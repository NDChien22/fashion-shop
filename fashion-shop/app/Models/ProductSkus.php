<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSkus extends Model
{
    use HasFactory;

    protected $table = 'product_skuses';

    protected $fillable = [
        'product_id',
        'sku',
        'size',
        'color',
        'stock',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
