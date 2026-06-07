<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'phone_number',
        'address',
        'gender',
        'birthday',
        'avatar',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gender' => 'string',
            'birthday' => 'date',
        ];
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employees::class, 'user_id');
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'user_id');
    }

    public function supportConversations(): HasMany
    {
        return $this->hasMany(SupportConversation::class, 'user_id');
    }

    public function supportMessages(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'sender_id');
    }

    public function orderFeedbacks(): HasMany
    {
        return $this->hasMany(OrderFeedback::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function staffOrders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::class, Employees::class, 'user_id', 'staff_id', 'id', 'id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class, 'user_id');
    }

    public function customerMembershipLevels(): HasMany
    {
        return $this->hasMany(CustomerMembershipLevel::class, 'user_id');
    }

    public function orderReturnRequests(): HasMany
    {
        return $this->hasMany(OrderReturnRequest::class, 'user_id');
    }
}
