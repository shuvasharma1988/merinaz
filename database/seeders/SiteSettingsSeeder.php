<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Business Information
            ['key' => 'business_name', 'value' => 'Merinaz', 'group' => 'general', 'type' => 'text'],
            ['key' => 'address', 'value' => '1768 Columbia Rd NW, Floor 2 Washington DC, 20009', 'group' => 'contact', 'type' => 'text'],
            ['key' => 'phone', 'value' => '2024692869', 'group' => 'contact', 'type' => 'text'],
            ['key' => 'email', 'value' => 'merinazofficial@gmail.com', 'group' => 'contact', 'type' => 'text'],

            // Business Hours
            ['key' => 'business_hours', 'value' => [
                'monday_friday' => '10:00 AM - 8:00 PM',
                'saturday' => '10:00 AM - 8:00 PM',
                'sunday' => '10:00 AM - 7:00 PM',
            ], 'group' => 'general', 'type' => 'json'],

            // Social Links
            ['key' => 'social_facebook', 'value' => 'https://www.facebook.com/merinazofficial', 'group' => 'social', 'type' => 'text'],
            ['key' => 'social_instagram', 'value' => 'https://www.instagram.com/lipikachhetri', 'group' => 'social', 'type' => 'text'],
            ['key' => 'social_tiktok', 'value' => 'https://www.tiktok.com/@merinaz07', 'group' => 'social', 'type' => 'text'],

            // Booking Policy
            ['key' => 'cancellation_window_hours', 'value' => 6, 'group' => 'booking', 'type' => 'integer'],
            ['key' => 'deposit_percentage', 'value' => 0, 'group' => 'booking', 'type' => 'integer'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::set(
                $setting['key'],
                $setting['value'],
                $setting['group'],
                $setting['type']
            );
        }
    }
}
