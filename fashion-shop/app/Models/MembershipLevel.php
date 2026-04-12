<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipLevel extends Model
{
    protected $fillable = [
        'name',
        'min_points',
        'point_conversion_rate',
        'discount_rate',
    ];

    public function customerMembershipLevels()
    {
        return $this->hasMany(CustomerMembershipLevel::class, 'membership_level_id');
    }
}
