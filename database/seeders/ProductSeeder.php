<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $cats = Category::pluck('id', 'slug');

        // name(bn), label(en for image), category, price, old_price, rating, reviews, [flags]
        // flags: F = flash sale, P = popular, X = featured (hero-worthy)
        $rows = [
            // --- Flash sale (electronics) ---
            ['সেলফি স্টিক', 'Selfie Stick', 'electronics', 599, 999, 4, 1, 'FP'],
            ['এলইডি ডেস্ক ল্যাম্প', 'LED Desk Lamp', 'electronics', 749, 1200, 4, 3, 'F'],
            ['ওয়্যারলেস মাউস', 'Wireless Mouse', 'electronics', 899, 1499, 5, 2, 'FP'],
            ['পাওয়ার ব্যাংক', 'Power Bank', 'electronics', 1299, 2000, 5, 0, 'FX'],
            ['ব্লুটুথ ইয়ারবাডস', 'Earbuds', 'electronics', 1599, 2500, 5, 1, 'FPX'],
            ['স্মার্ট ওয়াচ GT3', 'Smart Watch', 'electronics', 2499, 3999, 5, 0, 'FX'],

            // --- Electronics (popular) ---
            ['স্মার্ট প্লাগ', 'Smart Plug', 'electronics', 899, 1300, 4, 2, 'P'],
            ['গার্মেন্টস স্টিমার', 'Garment Steamer', 'electronics', 2999, 4500, 5, 2, 'P'],
            ['হেডফোন', 'Headphone', 'electronics', 3499, 5000, 4, 0, 'P'],
            ['গেমিং মাউস', 'Gaming Mouse', 'electronics', 1299, 1900, 5, 0, 'P'],
            ['গেমিং কীবোর্ড', 'Gaming Keyboard', 'electronics', 2499, 3800, 5, 0, 'P'],
            ['সাউন্ড বার', 'Sound Bar', 'electronics', 5999, 8500, 4, 0, 'PX'],
            ['এলইডি টিভি ৩২"', 'LED TV 32', 'electronics', 18999, 25000, 5, 0, 'PX'],

            // --- Home & Kitchen (popular) ---
            ['মিক্সিং বোল', 'Mixing Bowl', 'home-kitchen', 699, 1000, 5, 11, 'P'],
            ['কিচেন টুলস', 'Kitchen Tools', 'home-kitchen', 999, 1500, 5, 3, 'P'],
            ['স্টোরেজ কন্টেইনার', 'Storage Box', 'home-kitchen', 799, 1200, 5, 0, 'P'],
            ['ব্লেন্ডার জার', 'Blender Jar', 'home-kitchen', 1299, 1900, 5, 0, 'P'],
            ['কাটলারি সেট', 'Cutlery Set', 'home-kitchen', 1599, 2400, 4, 0, 'P'],
            ['ফ্রাইং প্যান', 'Frying Pan', 'home-kitchen', 2499, 3800, 5, 0, 'P'],
            ['মিক্সার গ্রাইন্ডার', 'Mixer Grinder', 'home-kitchen', 4599, 6200, 5, 0, ''],
            ['এয়ার ফ্রায়ার', 'Air Fryer', 'home-kitchen', 6599, 8999, 5, 0, 'X'],
            ['ইলেকট্রিক কেটলি', 'Electric Kettle', 'home-kitchen', 1899, 2800, 5, 0, ''],
            ['ডিজিটাল স্কেল', 'Digital Scale', 'home-kitchen', 999, 1500, 4, 0, ''],

            // --- Baby care (popular) ---
            ['বেবি সাবান', 'Baby Soap', 'baby-care', 199, 299, 5, 1, 'P'],
            ['বেবি টাওয়েল', 'Baby Towel', 'baby-care', 399, 599, 4, 0, 'P'],
            ['বেবি ওয়াইপস', 'Baby Wipes', 'baby-care', 249, 399, 5, 0, 'P'],
            ['বেবি লোশন', 'Baby Lotion', 'baby-care', 349, 550, 5, 0, 'P'],
            ['বেবি ফুড', 'Baby Food', 'baby-care', 299, 450, 5, 0, 'P'],
            ['বেবি ডায়াপার', 'Baby Diaper', 'baby-care', 499, 799, 5, 0, 'P'],

            // --- Food ---
            ['তাজা ফল প্যাক', 'Fruit Pack', 'food', 699, 1000, 4, 0, 'P'],
            ['বিস্কুট কম্বো', 'Biscuit Combo', 'food', 349, 500, 4, 0, 'P'],
            ['অলিভ অয়েল', 'Olive Oil', 'food', 1599, 2200, 5, 0, 'P'],
            ['বাসমতি রাইস', 'Basmati Rice', 'food', 899, 1200, 5, 0, 'P'],
            ['মধু', 'Honey', 'food', 599, 899, 5, 0, 'PX'],
            ['প্রিমিয়াম চা', 'Premium Tea', 'food', 399, 599, 5, 0, ''],
            ['খেজুর প্রিমিয়াম', 'Premium Dates', 'food', 799, 1100, 5, 0, ''],

            // --- Cloth ---
            ['প্রিমিয়াম টি-শার্ট', 'T-Shirt', 'cloth', 549, 850, 4, 0, 'X'],
            ['ক্যাজুয়াল পাঞ্জাবি', 'Panjabi', 'cloth', 1299, 1900, 5, 0, ''],
            ['ডেনিম জিন্স', 'Denim Jeans', 'cloth', 1499, 2200, 4, 0, ''],
            ['ফরমাল শার্ট', 'Formal Shirt', 'cloth', 999, 1500, 5, 0, ''],
            ['উইন্টার হুডি', 'Winter Hoodie', 'cloth', 1199, 1800, 5, 0, ''],
        ];

        $i = 0;
        foreach ($rows as $r) {
            [$name, $label, $catSlug, $price, $old, $rating, $reviews, $flags] = $r;
            $i++;

            $img = 'https://placehold.co/600x600/e6f2f0/008060?text='.rawurlencode($label);

            Product::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($label).'-'.$i],
                [
                    'category_id' => $cats[$catSlug] ?? null,
                    'name' => $name,
                    'description' => "প্রিমিয়াম মানের {$name}। ১০০% অরিজিনাল ও খাঁটি পণ্য। ক্যাশ অন ডেলিভারিতে সারা দেশে ডেলিভারি করা হয়।",
                    'image' => $img,
                    'price' => $price,
                    'old_price' => $old,
                    'stock' => 100,
                    'sku' => 'ODK-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                    'rating' => $rating,
                    'reviews_count' => $reviews,
                    'is_flash_sale' => str_contains($flags, 'F'),
                    'is_popular' => str_contains($flags, 'P'),
                    'is_featured' => str_contains($flags, 'X'),
                    'is_active' => true,
                    'sort_order' => $i,
                ]
            );
        }
    }
}
