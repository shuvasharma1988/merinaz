<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\StaffProfile;
use App\Models\Location;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Fetch dependencies
        $staffRole = Role::firstOrCreate(
            ['slug' => 'staff'],
            ['name' => 'Staff', 'description' => 'Service Provider']
        );

        $locations = Location::pluck('id')->toArray();
        if (empty($locations)) {
            // Fallback if locations aren't seeded yet
            $locations = [1, 2];
        }

        $allServices = Service::all();

        // 2. Define 12 unique staff members with varied rules
        $staffMembers = [
            ['name' => 'Lupita', 'title' => 'Threading Expert', 'locs' => [$locations[0] ?? 1], 'off' => ['sun', 'mon'], 'shift' => ['10:00:00', '17:00:00'], 'keyword' => 'Thread'],
            ['name' => 'Lilly', 'title' => 'Esthetician', 'locs' => [$locations[1] ?? 2], 'off' => ['tue', 'wed'], 'shift' => ['11:00:00', '19:00:00'], 'keyword' => 'Facial'],
            ['name' => 'Ruche', 'title' => 'Skincare Specialist', 'locs' => $locations, 'off' => ['sun', 'sat'], 'shift' => ['09:00:00', '15:00:00'], 'keyword' => 'Facial'],
            ['name' => 'Lipika', 'title' => 'Senior Esthetician', 'locs' => [$locations[0] ?? 1], 'off' => ['thu', 'fri'], 'shift' => ['12:00:00', '20:00:00'], 'keyword' => 'Microdermabrasion'],
            ['name' => 'Nadia', 'title' => 'Sugaring Specialist', 'locs' => [$locations[1] ?? 2], 'off' => ['sun', 'mon'], 'shift' => ['10:00:00', '18:00:00'], 'keyword' => 'Sugar'],
            ['name' => 'Maria', 'title' => 'Waxing Specialist', 'locs' => [$locations[0] ?? 1], 'off' => ['mon', 'tue'], 'shift' => ['09:00:00', '17:00:00'], 'keyword' => 'Wax'],
            ['name' => 'Chloe', 'title' => 'Lash Technician', 'locs' => $locations, 'off' => ['wed', 'thu'], 'shift' => ['11:00:00', '19:00:00'], 'keyword' => 'Lash'],
            ['name' => 'Sarah', 'title' => 'Trimming Specialist', 'locs' => [$locations[1] ?? 2], 'off' => ['sun', 'sat'], 'shift' => ['08:00:00', '14:00:00'], 'keyword' => 'Trim'],
            ['name' => 'Emma', 'title' => 'Threading Expert', 'locs' => [$locations[0] ?? 1], 'off' => ['tue', 'wed'], 'shift' => ['13:00:00', '20:00:00'], 'keyword' => 'Thread'],
            ['name' => 'Olivia', 'title' => 'Esthetician', 'locs' => [$locations[1] ?? 2], 'off' => ['mon', 'thu'], 'shift' => ['10:00:00', '18:00:00'], 'keyword' => 'Facial'],
            ['name' => 'Ava', 'title' => 'Waxing Specialist', 'locs' => $locations, 'off' => ['fri', 'sat'], 'shift' => ['12:00:00', '20:00:00'], 'keyword' => 'Wax'],
            ['name' => 'Mia', 'title' => 'Tinting & Brow Artist', 'locs' => [$locations[0] ?? 1], 'off' => ['sun', 'wed'], 'shift' => ['09:00:00', '16:00:00'], 'keyword' => 'Tint'],
        ];

        $daysOfWeek = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

        DB::transaction(function () use ($staffMembers, $staffRole, $allServices, $daysOfWeek) {
            foreach ($staffMembers as $index => $staff) {
                $email = strtolower($staff['name']) . '@merinaz.com';

                // 3. Create User
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $staff['name'],
                        'password' => Hash::make(Str::password(12)),
                        'email_verified_at' => now(),
                    ]
                );

                // Attach staff role
                if (!$user->roles()->where('role_id', $staffRole->id)->exists()) {
                    $user->roles()->attach($staffRole->id);
                }

                // 4. Create Staff Profile
                $profile = StaffProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'phone' => '202-555-01' . str_pad($index, 2, '0', STR_PAD_LEFT),
                        'job_title' => $staff['title'],
                        'bio' => "Professional {$staff['title']} with years of experience providing top-tier service.",
                        'is_active' => true,
                    ]
                );

                // 5. Attach Locations (Pivot: location_staff uses staff_id mapped to user_id)
                foreach ($staff['locs'] as $locId) {
                    DB::table('location_staff')->updateOrInsert([
                        'location_id' => $locId,
                        'staff_id' => $user->id,
                    ], [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // 6. Attach Selective Services (Pivot: service_staff uses staff_profile_id)
                if ($allServices->isNotEmpty()) {
                    $matchedServices = $allServices->filter(function ($service) use ($staff) {
                        return stripos($service->name, $staff['keyword']) !== false;
                    })->pluck('id')->toArray();

                    // Fallback if no exact keyword match, assign a random service so they have something
                    if (empty($matchedServices)) {
                        $matchedServices = [$allServices->random()->id];
                    }

                    $profile->services()->sync($matchedServices);
                }

                // 7. Create Working Hours (7-day grid)
                foreach ($daysOfWeek as $day) {
                    $isDayOff = in_array($day, $staff['off']);

                    // Generate realistic breaks (e.g., 1 hour break halfway through shift)
                    $startTime = $isDayOff ? null : $staff['shift'][0];
                    $endTime = $isDayOff ? null : $staff['shift'][1];

                    $breakStart = null;
                    $breakEnd = null;
                    if (!$isDayOff) {
                        $startObj = Carbon::parse($startTime);
                        $breakStart = $startObj->copy()->addHours(4)->format('H:i:s');
                        $breakEnd = $startObj->copy()->addHours(5)->format('H:i:s');
                    }

                    DB::table('staff_working_hours')->updateOrInsert(
                        [
                            'staff_profile_id' => $profile->id,
                            'day_of_week' => $day,
                        ],
                        [
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'break_start_time' => $breakStart,
                            'break_end_time' => $breakEnd,
                            'is_day_off' => $isDayOff,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                // 8. Assign Blackout Dates (Simulated upcoming time off)
                // Offset their time off based on their index so they don't all take the same days
                $dateOff = Carbon::now()->addDays(10 + $index)->format('Y-m-d');
                DB::table('staff_blackout_dates')->updateOrInsert(
                    [
                        'staff_profile_id' => $profile->id,
                        'date' => $dateOff,
                    ],
                    [
                        'reason' => 'Scheduled Vacation / Personal Time',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        });
    }
}
