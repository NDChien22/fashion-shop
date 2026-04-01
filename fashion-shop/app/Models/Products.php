<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'product_code',
        'category_id',
        'collection_id',
        'name',
        'slug',
        'description',
        'base_price',
        'main_image_url',
        'gallery_image_urls',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'gallery_image_urls' => 'array',
        'is_active' => 'boolean',
    ];

    public function sluggable(): array
    {
        return 
        [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collections::class, 'collection_id');
    }

    public function skus(): HasMany
    {
        return $this->hasMany(ProductSkus::class, 'product_id');
    }
}
