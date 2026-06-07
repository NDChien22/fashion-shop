<?php

namespace App\Models;

use App\Enums\OrderReturnRequestStatus;
use App\Enums\OrderReturnRequestType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturnRequest extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'request_type',
        'reason',
        'details',
        'evidence_images',
        'status',
        'admin_note',
        'admin_id',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'request_type' => OrderReturnRequestType::class,
            'status' => OrderReturnRequestStatus::class,
            'evidence_images' => 'array',
            'resolved_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'admin_id');
    }
}
