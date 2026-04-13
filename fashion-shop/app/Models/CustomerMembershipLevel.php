<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerMembershipLevel extends Model
{
    protected $fillable = [
        'user_id',
        'customer_code',
        'membership_level_id',
        'points',
    ];

    public function membershipLevel()
    {
        return $this->belongsTo(MembershipLevel::class, 'membership_level_id');
    }
}
