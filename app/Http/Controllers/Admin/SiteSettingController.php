<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function edit()
    {
        $settings = SiteSetting::getAll();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $fields = [
            'site_name', 'site_tagline', 'topbar_text', 'phone', 'email', 'address',
            'facebook', 'instagram', 'youtube', 'tiktok', 'telegram', 'whatsapp',
            'shipping_inside_dhaka', 'shipping_outside_dhaka', 'footer_text',
        ];

        foreach ($fields as $field) {
            SiteSetting::set($field, $request->input($field));
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
