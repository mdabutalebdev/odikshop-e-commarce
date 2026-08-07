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
            'site_tagline' => 'অনলাইনে কেনাকাটা করুন',
            'topbar_text' => 'ফ্রি ডেলিভারি ও ক্যাশ অন ডেলিভারি',
            'phone' => '01700-000000',
            'email' => 'support@odikshop.com',
            'address' => 'ঢাকা, বাংলাদেশ',
            'facebook' => 'https://facebook.com',
            'instagram' => 'https://instagram.com',
            'whatsapp' => '01700000000',
            'shipping_inside_dhaka' => '60',
            'shipping_outside_dhaka' => '120',
            'footer_text' => 'ODHIK SHOP - আপনার বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম।',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
