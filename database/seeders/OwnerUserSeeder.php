<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerUserSeeder extends Seeder
{
    public function run(): void
    {
        $ownerUser = User::firstOrCreate(
            ['email' => env('OWNER_EMAIL', 'admin@example.com')],
            [
                'name' => env('OWNER_NAME', 'Admin User'),
                'password' => Hash::make(env('OWNER_PASSWORD', 'password')),
                'email_verified_at' => now(),
            ]
        );

        $ownerRole = Role::where('slug', 'owner')->first();
        if ($ownerRole && ! $ownerUser->hasRole('owner')) {
            $ownerUser->roles()->attach($ownerRole);
        }
    }
}
