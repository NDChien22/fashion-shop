<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_code',
        'total_amount',
        'discount_amount',
        'final_amount',
        'status',
        'shipping_status',
        'payment_method',
        // Guest fields (for guest checkout)
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_address',
        'guest_note',
        'staff_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function orderVouchers(): HasMany
    {
        return $this->hasMany(OrderVoucher::class, 'order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'staff_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id');
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(OrderFeedback::class, 'order_id');
    }

    public function returnRequest(): HasOne
    {
        return $this->hasOne(OrderReturnRequest::class, 'order_id');
    }

    // Accessors: prefer user data when order belongs to a user, otherwise fall back to guest fields / DB values
    public function getCustomerNameAttribute(?string $value): string
    {
        if ($this->relationLoaded('user') || $this->user) {
            return (string) ($this->user->full_name ?? $value ?? '');
        }

        return (string) ($this->attributes['customer_name'] ?? $this->attributes['guest_name'] ?? $value ?? '');
    }

    public function getCustomerEmailAttribute(?string $value): string
    {
        if ($this->relationLoaded('user') || $this->user) {
            return (string) ($this->user->email ?? $value ?? '');
        }

        return (string) ($this->attributes['customer_email'] ?? $this->attributes['guest_email'] ?? $value ?? '');
    }

    public function getCustomerPhoneAttribute(?string $value): string
    {
        if ($this->relationLoaded('user') || $this->user) {
            return (string) ($this->user->phone_number ?? $value ?? '');
        }

        return (string) ($this->attributes['customer_phone'] ?? $this->attributes['guest_phone'] ?? $value ?? '');
    }

    public function getShippingAddressAttribute(?string $value): string
    {
        if ($this->relationLoaded('user') || $this->user) {
            return (string) ($this->user->address ?? $value ?? '');
        }

        return (string) ($this->attributes['shipping_address'] ?? $this->attributes['guest_address'] ?? $value ?? '');
    }
}
