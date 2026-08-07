<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name_en' => 'Electronics', 'name_bn' => 'ইলেকট্রনিক্স', 'slug' => 'electronics', 'icon' => 'fa-solid fa-laptop', 'show_in_nav' => true, 'sort_order' => 1],
            ['name_en' => 'Mobile Accessories', 'name_bn' => 'মোবাইল অ্যাক্সেসরিজ', 'slug' => 'mobile-accessories', 'icon' => 'fa-solid fa-mobile-screen', 'show_in_nav' => true, 'sort_order' => 2],
            ['name_en' => 'Kitchen Gadgets', 'name_bn' => 'কিচেন গ্যাজেট', 'slug' => 'kitchen-gadgets', 'icon' => 'fa-solid fa-blender', 'show_in_nav' => true, 'sort_order' => 3],
            ['name_en' => 'Home Appliances', 'name_bn' => 'হোম অ্যাপ্লায়েন্স', 'slug' => 'home-appliances', 'icon' => 'fa-solid fa-tv', 'show_in_nav' => true, 'sort_order' => 4],
            ['name_en' => 'Fans & Cooling Devices', 'name_bn' => 'ফ্যান ও কুলিং ডিভাইস', 'slug' => 'fans-cooling', 'icon' => 'fa-solid fa-fan', 'show_in_nav' => false, 'sort_order' => 5],
            ['name_en' => 'Sports & Fitness', 'name_bn' => 'হেলথ্ ও স্পোর্টস', 'slug' => 'sports-fitness', 'icon' => 'fa-solid fa-dumbbell', 'show_in_nav' => false, 'sort_order' => 6],
            ['name_en' => 'Tools & Hardware', 'name_bn' => 'টুলস ও হার্ডওয়্যার', 'slug' => 'tools-hardware', 'icon' => 'fa-solid fa-screwdriver-wrench', 'show_in_nav' => false, 'sort_order' => 7],
            ['name_en' => 'Travel & Outdoor', 'name_bn' => 'ট্রাভেল ও আউটডোর', 'slug' => 'travel-outdoor', 'icon' => 'fa-solid fa-suitcase', 'show_in_nav' => false, 'sort_order' => 8],
            ['name_en' => 'Fashion', 'name_bn' => 'ফ্যাশন', 'slug' => 'fashion', 'icon' => 'fa-solid fa-shirt', 'show_in_nav' => false, 'sort_order' => 9],
            ['name_en' => 'Baby & Kids', 'name_bn' => 'বাচ্চাদের পণ্য', 'slug' => 'baby-kids', 'icon' => 'fa-solid fa-baby', 'show_in_nav' => false, 'sort_order' => 10],
            ['name_en' => 'Personal Care', 'name_bn' => 'পার্সোনাল কেয়ার', 'slug' => 'personal-care', 'icon' => 'fa-solid fa-pump-soap', 'show_in_nav' => false, 'sort_order' => 11],
            ['name_en' => 'Others', 'name_bn' => 'অন্যান্য', 'slug' => 'others', 'icon' => 'fa-solid fa-ellipsis', 'show_in_nav' => false, 'sort_order' => 12],
        ];

        foreach ($categories as $c) {
            Category::updateOrCreate(['slug' => $c['slug']], $c);
        }
    }
}
