<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'ODHIK SHOP',
            'site_tagline' => 'Your Trusted Online Shopping in Bangladesh',
            'topbar_text' => 'Free Delivery all over Bangladesh & Cash on Delivery',
            'phone' => '+8801770-980028',
            'whatsapp' => '8801575513701',
            'email' => 'odhikshop@gmail.com',
            'address' => 'Banasree, Rampura, Dhaka-1219',
            'facebook' => 'https://facebook.com/odhikshop',
            'instagram' => 'https://instagram.com/odhikshop',
            'youtube' => 'https://youtube.com/@odhikshop',
            'tiktok' => 'https://tiktok.com/@odhikshop',
            'telegram' => 'https://t.me/odhikshop',
            'shipping_inside_dhaka' => '60',
            'shipping_outside_dhaka' => '120',
            'footer_text' => 'ODHIK SHOP E-commerce BD — your trusted online shopping platform delivering across Bangladesh.',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
