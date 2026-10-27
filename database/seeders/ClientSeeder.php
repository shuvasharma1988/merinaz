<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\ClientProfile;
use App\Models\ClientNote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $customerRole = Role::firstOrCreate(
            ['slug' => 'customer'],
            ['name' => 'Customer', 'description' => 'Client']
        );

        // Fetch a staff member to author the notes. If none exist, we will gracefully skip note creation.
        $staff = User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['staff', 'manager', 'owner']);
        })->first();

        $clients = [
            [
                'name' => 'Emma Thompson',
                'email' => 'emma@example.com',
                'phone' => '202-555-0201',
                'gender' => 'female',
                'source' => 'online',
                'tag' => 'VIP',
                'has_medical_issue' => true,
                'notes' => [
                    ['note' => 'Allergic to specific wax types. Patch test required.', 'visibility' => 'team'],
                    ['note' => 'Prefers evening appointments.', 'visibility' => 'manager'],
                ]
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael@example.com',
                'phone' => '202-555-0202',
                'gender' => 'male',
                'source' => 'walk-in',
                'tag' => 'New',
                'has_medical_issue' => false,
                'notes' => [
                    ['note' => 'First time getting a back wax, was a bit nervous.', 'visibility' => 'private']
                ]
            ],
            [
                'name' => 'Sophia Martinez',
                'email' => 'sophia@example.com',
                'phone' => '202-555-0203',
                'gender' => 'female',
                'source' => 'referral',
                'tag' => null,
                'has_medical_issue' => false,
                'notes' => []
            ],
            [
                'name' => 'James Wilson',
                'email' => 'james@example.com',
                'phone' => '202-555-0204',
                'gender' => 'male',
                'source' => 'online',
                'tag' => 'Regular',
                'has_medical_issue' => false,
                'notes' => [
                    ['note' => 'Always books sugaring for chest.', 'visibility' => 'team']
                ]
            ],
            [
                'name' => 'Olivia Brown',
                'email' => 'olivia@example.com',
                'phone' => '202-555-0205',
                'gender' => 'female',
                'source' => 'walk-in',
                'tag' => 'VIP',
                'has_medical_issue' => false,
                'notes' => [
                    ['note' => 'Complained about wait time once, ensure prompt service.', 'visibility' => 'manager']
                ]
            ],
            [
                'name' => 'William Taylor',
                'email' => 'william@example.com',
                'phone' => '202-555-0206',
                'gender' => 'male',
                'source' => 'online',
                'tag' => 'New',
                'has_medical_issue' => true,
                'notes' => [
                    ['note' => 'Has sensitive skin, use hard wax only.', 'visibility' => 'team']
                ]
            ],
        ];

        foreach ($clients as $index => $clientData) {
            // 1. Create User
            $user = User::firstOrCreate(
                ['email' => $clientData['email']],
                [
                    'name' => $clientData['name'],
                    'password' => Hash::make(Str::password(12)),
                    'email_verified_at' => now(),
                ]
            );

            // Attach customer role
            if (!$user->roles()->where('role_id', $customerRole->id)->exists()) {
                $user->roles()->attach($customerRole->id);
            }

            // 2. Create Client Profile
            ClientProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'date_of_birth' => Carbon::now()->subYears(25 + $index)->format('Y-m-d'),
                    'gender' => $clientData['gender'],
                    'phone' => $clientData['phone'],
                    'source' => $clientData['source'],
                    'status' => 'active',
                    'tag' => $clientData['tag'],
                    'marketing_consent' => (bool)rand(0, 1),
                    'sms_consent' => (bool)rand(0, 1),
                    'email_consent' => (bool)rand(0, 1),
                    'has_medical_issue' => $clientData['has_medical_issue'],
                ]
            );

            // 3. Create Client Notes
            if ($staff && !empty($clientData['notes'])) {
                foreach ($clientData['notes'] as $noteData) {
                    ClientNote::firstOrCreate([
                        'client_id' => $user->id,
                        'staff_id' => $staff->id,
                        'note' => $noteData['note'],
                        'visibility' => $noteData['visibility'],
                    ]);
                }
            }
        }
    }
}
