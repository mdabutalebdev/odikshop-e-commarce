<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        Banner::truncate();

        $banners = [
            // Center slider (type = hero) — can hold multiple slides
            [
                'title' => 'মূল স্লাইড',
                'image' => 'slide_1_1785082093_demo1.webp',
                'link' => '/shop',
                'type' => 'hero',
                'sort_order' => 1,
            ],
            // Left promo banner
            [
                'title' => 'বাম প্রোমো',
                'image' => 'left_banner_1785083685_bem.webp',
                'link' => '/shop?category=electronics',
                'type' => 'left',
                'sort_order' => 1,
            ],
            // Right promo banner
            [
                'title' => 'ডান প্রোমো',
                'image' => 'right_banner_1785084278_dan.webp',
                'link' => '/shop?category=cloth',
                'type' => 'right',
                'sort_order' => 1,
            ],
        ];

        foreach ($banners as $b) {
            Banner::create($b);
        }
    }
}
