<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Data sourced from the provided address image
        $locations = [
            [
                'name' => 'Columbia Road',
                'address' => '1768 Columbia Rd NW, Floor 2 Washington DC, 20009',
                'phone' => '2024692869',
                'email' => 'merinazofficial@gmail.com',
            ],
            [
                'name' => 'Wisconsin Avenue',
                'address' => '3500 Wisconsin Avenue NW, Suite A Washington, DC 20016',
                'phone' => '2028044185',
                'email' => 'merinazofficial@gmail.com',
            ],
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate(
                ['slug' => Str::slug($location['name'])],
                array_merge($location, ['is_active' => true])
            );
        }
    }
}
