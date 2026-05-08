<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderFeedback extends Model
{
    use HasFactory;

    protected $table = 'order_feedbacks';

    protected $fillable = [
        'order_id',
        'product_id',
        'user_id',
        'rating',
        'content',
        'admin_reply',
        'admin_replied_at',
        'admin_reply_by',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'admin_replied_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function adminReplyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_reply_by');
    }
}
