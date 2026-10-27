<?php

namespace Database\Seeders;

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
        $this->call([
            RoleAndPermissionSeeder::class,
            OwnerUserSeeder::class,
            SiteSettingsSeeder::class,
            LocationSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            StaffSeeder::class,
            ClientSeeder::class,
            ReviewSeeder::class,
            BlogCategorySeeder::class,
            PostSeeder::class,
            ContentSeeder::class,
        ]);
    }
}
