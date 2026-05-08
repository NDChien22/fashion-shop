<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderVoucher extends Model
{
    protected $fillable = [
        'order_id',
        'voucher_id',
        'discount_amount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];
}
