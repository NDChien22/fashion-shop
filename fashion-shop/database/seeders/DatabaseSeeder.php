<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Categories;
use App\Models\Collections;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        $category = Categories::query()->firstOrCreate(
            ['slug' => 'summer-collection'],
            [
                'name' => 'Summer Collection',
                'is_active' => 1,
            ]
        );

        $collection = Collections::query()->firstOrCreate(
            ['slug' => 'new-arrival'],
            [
                'name' => 'New Arrival',
                'thumbnail_url' => null,
                'description' => 'Bộ sưu tập mới cho trang chủ.',
                'is_active' => 1,
            ]
        );

        $banners = [
            [
                'title' => 'Mở bán bộ sưu tập mới',
                'banner_type' => 'all',
                'category_id' => null,
                'collection_id' => null,
                'image_url' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1600',
                'is_active' => true,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'title' => 'Ưu đãi thời trang nữ',
                'banner_type' => 'category',
                'category_id' => $category->id,
                'collection_id' => null,
                'image_url' => 'https://images.unsplash.com/photo-1485462537746-965f33f7f6a7?w=1600',
                'is_active' => true,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'title' => 'Khám phá bộ sưu tập mới',
                'banner_type' => 'collection',
                'category_id' => null,
                'collection_id' => $collection->id,
                'image_url' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=1600',
                'is_active' => true,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'title' => 'Flash sale cuối tuần',
                'banner_type' => 'all',
                'category_id' => null,
                'collection_id' => null,
                'image_url' => 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=1600',
                'is_active' => true,
                'start_date' => null,
                'end_date' => null,
            ],
        ];

        foreach ($banners as $bannerData) {
            Banner::query()->firstOrCreate(
                ['title' => $bannerData['title']],
                $bannerData
            );
        }
    }
}
