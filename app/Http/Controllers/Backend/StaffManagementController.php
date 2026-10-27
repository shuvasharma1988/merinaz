<?php

namespace App\Http\Controllers\Backend;

use App\Events\Backend\StaffPermissionsUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreStaffRequest;
use App\Http\Requests\Backend\UpdateStaffRequest;
use App\Models\Location;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);
        $users = User::with('roles')->whereHas('roles', function ($q) {
            $q->whereIn('slug', ['owner', 'manager', 'staff']);
        })->get();

        return view('admin.staff.index', compact('users'));
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        $roles = auth()->user()->hasRole('owner')
            ? Role::all()
            : Role::where('slug', 'staff')->get();

        $permissions = auth()->user()->hasRole('owner')
            ? Permission::all()->groupBy('group')
            : auth()->user()->getAllPermissions()->groupBy('group');

        $services = Service::all();
        $locations = Location::all();

        // Inject the Standard 10-5 Schedule Baseline
        $formattedHours = [];
        $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

        foreach ($days as $day) {
            $formattedHours[$day] = [
                'is_day_off' => ($day === 'sun'), // Default Sunday to off
                'start_time' => '10:00',
                'end_time' => '17:00',
                'break_start_time' => '13:00',
                'break_end_time' => '13:30',
            ];
        }

        return view('admin.staff.create', compact('roles', 'permissions', 'services', 'locations', 'formattedHours'));
    }

    public function store(StoreStaffRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);
        $data = $request->validated();

        DB::transaction(function () use ($data, $request) {
            // 1. Create Core User
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->roles()->attach($data['role_id']);
            $user->permissions()->sync($data['permissions'] ?? []);

            // 2. Create Staff Profile
            $profile = $user->staff()->create([
                'phone' => $data['phone'] ?? null,
                'bio' => $data['bio'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            // Sync Relationships
            if (isset($data['services'])) {
                $profile->services()->sync($data['services']);
            }
            if (isset($data['locations'])) {
                $profile->locations()->sync($data['locations']);
            }

            // 3. Create Working Hours (7-day grid)
            foreach (['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'] as $day) {
                $dayData = $data['hours'][$day] ?? [];

                $parseTime = function ($timeString) {
                    if (empty($timeString)) {
                        return null;
                    }
                    try {
                        return Carbon::parse($timeString)->format('H:i:s');
                    } catch (\Exception $e) {
                        return null;
                    }
                };

                $profile->workingHours()->create([
                    'day_of_week' => $day,
                    'is_day_off' => isset($dayData['is_day_off']) ? (bool) $dayData['is_day_off'] : false,
                    'start_time' => $parseTime($dayData['start_time'] ?? null),
                    'end_time' => $parseTime($dayData['end_time'] ?? null),
                    'break_start_time' => $parseTime($dayData['break_start_time'] ?? null),
                    'break_end_time' => $parseTime($dayData['break_end_time'] ?? null),
                ]);
            }

            // 4. Create Blackout Dates
            if (! empty($data['blackout_dates'])) {
                foreach ($data['blackout_dates'] as $blackout) {
                    $profile->blackoutDates()->create([
                        'date' => $blackout['date'],
                        'reason' => $blackout['reason'] ?? null,
                    ]);
                }
            }

            event(new StaffPermissionsUpdated(auth()->user(), $user));
        });

        return redirect()->route('admin.staff.index')->with('success', 'Staff account created successfully.');
    }

    public function show(User $staff)
    {
        Gate::authorize('viewDetail', $staff);
        $staff->load(['roles', 'permissions', 'staff.services', 'staff.locations', 'staff.workingHours', 'staff.blackoutDates']);

        return view('admin.staff.details', ['user' => $staff]);
    }

    public function edit(User $staff)
    {
        Gate::authorize('update', $staff);

        $roles = Role::where('slug', '!=', 'owner')->get();
        $permissions = auth()->user()->hasRole('owner')
            ? Permission::all()->groupBy('group')
            : auth()->user()->getAllPermissions()->groupBy('group');

        $services = Service::all();
        $locations = Location::all();

        $staff->load(['roles', 'permissions', 'staff.services', 'staff.locations', 'staff.workingHours', 'staff.blackoutDates']);

        $formattedHours = [];
        if ($staff->staff && $staff->staff->workingHours) {
            foreach ($staff->staff->workingHours as $hour) {
                $formattedHours[$hour->day_of_week] = [
                    'is_day_off' => $hour->is_day_off,
                    'start_time' => $hour->start_time ? Carbon::parse($hour->start_time)->format('H:i') : null,
                    'end_time' => $hour->end_time ? Carbon::parse($hour->end_time)->format('H:i') : null,
                    'break_start_time' => $hour->break_start_time ? Carbon::parse($hour->break_start_time)->format('H:i') : null,
                    'break_end_time' => $hour->break_end_time ? Carbon::parse($hour->break_end_time)->format('H:i') : null,
                ];
            }
        }

        return view('admin.staff.edit', compact('staff', 'roles', 'permissions', 'services', 'locations', 'formattedHours'));
    }

    public function update(UpdateStaffRequest $request, User $staff)
    {
        Gate::authorize('update', $staff);
        $data = $request->validated();

        DB::transaction(function () use ($data, $staff, $request) {
            // 1. Update Core User
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];
            if (! empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }
            $staff->update($userData);

            $staff->roles()->sync([$data['role_id']]);
            $staff->permissions()->sync($data['permissions'] ?? []);

            // 2. Update/Create Staff Profile
            $profile = $staff->staff()->updateOrCreate(
                ['user_id' => $staff->id],
                [
                    'phone' => $data['phone'] ?? null,
                    'bio' => $data['bio'] ?? null,
                    'is_active' => $request->boolean('is_active'),
                ]
            );

            // Sync Relationships
            if (isset($data['services'])) {
                $profile->services()->sync($data['services']);
            }
            if (isset($data['locations'])) {
                $profile->locations()->sync($data['locations']);
            }

            // 3. Sync Working Hours
            foreach (['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'] as $day) {
                $dayData = $data['hours'][$day] ?? [];

                $parseTime = function ($timeString) {
                    if (empty($timeString)) {
                        return null;
                    }
                    try {
                        return Carbon::parse($timeString)->format('H:i:s');
                    } catch (\Exception $e) {
                        return null;
                    }
                };

                $profile->workingHours()->updateOrCreate(
                    ['day_of_week' => $day],
                    [
                        'is_day_off' => isset($dayData['is_day_off']) ? (bool) $dayData['is_day_off'] : false,
                        'start_time' => $parseTime($dayData['start_time'] ?? null),
                        'end_time' => $parseTime($dayData['end_time'] ?? null),
                        'break_start_time' => $parseTime($dayData['break_start_time'] ?? null),
                        'break_end_time' => $parseTime($dayData['break_end_time'] ?? null),
                    ]
                );
            }

            // 4. Sync Blackout Dates
            $incomingDates = collect($data['blackout_dates'] ?? [])->pluck('date')->toArray();

            $profile->blackoutDates()
                ->where('date', '>=', now()->toDateString())
                ->whereNotIn('date', $incomingDates)
                ->delete();

            if (! empty($data['blackout_dates'])) {
                foreach ($data['blackout_dates'] as $blackout) {
                    $profile->blackoutDates()->updateOrCreate(
                        ['date' => $blackout['date']],
                        ['reason' => $blackout['reason'] ?? null]
                    );
                }
            }
        });

        return redirect()->route('admin.staff.index')
            ->with('success', "Staff member '{$staff->name}' updated successfully.");
    }
}
