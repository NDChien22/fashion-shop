<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collections extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'name',
        'slug',
        'thumbnail_url',
        'description',
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

    public function products(): HasMany
    {
        return $this->hasMany(Products::class, 'collection_id');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'collection_id');
    }

    public function flashSales(): HasMany
    {
        return $this->hasMany(FlashSale::class, 'collection_id');
    }

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class, 'collection_id');
    }
}
