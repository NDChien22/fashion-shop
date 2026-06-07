<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categories extends Model
{
    use HasFactory,Sluggable;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'is_active',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Products::class, 'category_id');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'category_id');
    }

    public function flashSales(): HasMany
    {
        return $this->hasMany(FlashSale::class, 'category_id');
    }

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class, 'category_id');
    }
}
