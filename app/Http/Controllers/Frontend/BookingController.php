<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Service;
use App\Models\User;
use App\Models\Role;
use App\Models\Appointment;
use App\Services\Backend\BookingService;
use App\Services\Backend\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
Use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected AvailabilityService $availabilityService
    ) {}

    public function index(Request $request)
    {
        $locations = Location::where('is_active', true)->get();

        $servicesByCategory = Service::with('category')
            ->where('is_active', true)
            ->get()
            ->groupBy(function($service) {
                return $service->category ? $service->category->name : 'Uncategorized';
            });

        $selectedLocation = $request->query('location') ?? $locations->first()->id ?? null;

        return view('frontend.booking.index', compact('locations', 'servicesByCategory', 'selectedLocation'));
    }

    /**
     * API: Fetch dynamic available slots for the selected date and services.
     */
    public function getAvailableSlots(Request $request)
    {
        $locationId = $request->query('location_id');
        $date = $request->query('date');
        $staffId = $request->query('staff_id');
        $serviceId = $request->query('service_id');

        if (!$locationId || !$date || !$serviceId) {
            return response()->json([]);
        }

        // Auto-assign a capable staff member if "First Available" was chosen
        if (empty($staffId)) {
            $dayOfWeek = strtolower(\Carbon\Carbon::parse($date)->shortEnglishDayOfWeek);
            $staff = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('slug', ['owner', 'manager', 'staff']))
                ->whereHas('staff', function($q) use ($locationId, $dayOfWeek, $date, $serviceId) {
                    $q->whereHas('locations', fn($l) => $l->where('locations.id', $locationId))
                        ->whereHas('workingHours', fn($w) => $w->where('day_of_week', $dayOfWeek)->where('is_day_off', 0))
                        ->whereDoesntHave('blackoutDates', fn($b) => $b->where('date', $date))
                        ->whereHas('services', fn($s) => $s->where('services.id', $serviceId));
                })->first();

            if (!$staff) return response()->json([]);
            $staffId = $staff->id;
        }

        $service = \App\Models\Service::find($serviceId);
        $duration = (int) ($service->duration ?? $service->duration_minutes ?? 30);

        $slots = app(\App\Services\Backend\AvailabilityService::class)
            ->getAvailableTimeSlots($locationId, $staffId, $serviceId, $duration, $date);

        return response()->json($slots);
    }

    /**
     * Step 2: Validate Step 1 payload and display customer details form.
     */
    public function details(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            // Cleanly validate the single service
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'nullable|exists:users,id',
        ]);

        $location = \App\Models\Location::findOrFail($validated['location_id']);
        $selectedService = \App\Models\Service::findOrFail($validated['service_id']);

        return view('frontend.booking.details', compact('validated', 'location', 'selectedService'));
    }

    /**
     * Store the booking securely inside a Database Transaction.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:locations,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',

            // Cleanly validate the single service
            'service_id' => 'required|exists:services,id',

            'staff_id' => 'nullable|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'customer_notes' => 'nullable|string|max:1000',
            'terms_consent' => 'accepted',
        ]);

        if ($validator->fails()) {
            // FIX: Redirect all the way back to Step 1 securely with the errors.
            // This avoids the 405 GET error on the details route.
            return redirect()->route('booking.index')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        try {
            $appointment = DB::transaction(function () use ($validated) {

                $user = auth()->user();

                if (!$user) {
                    $user = User::firstOrCreate(
                        ['email' => $validated['email']],
                        [
                            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                            'password' => Hash::make(Str::random(16)),
                        ]
                    );

                    $customerRole = Role::where('slug', 'customer')->first();
                    if ($customerRole) {
                        $user->roles()->syncWithoutDetaching([$customerRole->id]);
                    }

                    $user->clientProfile()->firstOrCreate(
                        ['user_id' => $user->id],
                        ['phone' => $validated['phone']]
                    );
                }

                // Retrieve the service
                $service = Service::findOrFail($validated['service_id']);

                $duration = (int) ($service->duration ?? $service->duration_minutes ?? 30);

                $assignedStaffId = $validated['staff_id'];

                if (!$assignedStaffId) {
                    $assignedStaffId = $this->findFirstAvailableStaff(
                        $validated['location_id'],
                        $service->id,
                        $validated['date'],
                        $validated['time'],
                        $duration
                    );
                }

                // Construct the array that the backend BookingService demands
                $items = [
                    [
                        'service_id' => $service->id,
                        'staff_id' => $assignedStaffId,
                        'start_time' => $validated['time'],
                    ]
                ];

                $bookingData = [
                    'location_id' => $validated['location_id'],
                    'client_id' => $user->id,
                    'appointment_date' => $validated['date'],
                    'status' => 'pending',
                    'booking_source' => 'online',
                    'customer_notes' => $validated['customer_notes'],
                    'discount' => 0,
                    'tax' => 0,
                    'items' => $items,
                ];

                return $this->bookingService->createBooking($bookingData);
            });

            return redirect()->route('booking.confirmation', $appointment->booking_reference);

        } catch (\Exception $e) {
            return redirect()->route('booking.index')
                ->withInput()
                ->withErrors(['error' => 'Booking failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Step 4: Display Success Confirmation.
     */
    public function confirmation($reference)
    {
        $appointment = Appointment::with(['location', 'items.service', 'items.staff'])
            ->where('booking_reference', $reference)
            ->firstOrFail();

        return view('frontend.booking.confirmation', compact('appointment'));
    }

    /**
     * Helper: Auto-resolve staff if public user selects "First Available".
     */
    private function findFirstAvailableStaff(int $locationId, int $serviceId, string $date, string $startTime, int $duration)
    {
        $endTime = Carbon::parse($startTime)->addMinutes($duration)->format('H:i:s');
        $dayOfWeek = strtolower(Carbon::parse($date)->shortEnglishDayOfWeek);

        // Fetch staff who match location, service capacity, and have active working hours
        $staff = User::whereHas('staff', function ($profileQuery) use ($locationId, $dayOfWeek, $serviceId) {
            $profileQuery->whereHas('locations', fn($q) => $q->where('locations.id', $locationId))
                ->whereHas('services', fn($q) => $q->where('services.id', $serviceId))
                ->whereHas('workingHours', function ($hoursQuery) use ($dayOfWeek) {
                    $hoursQuery->where('day_of_week', $dayOfWeek)
                        ->where('is_day_off', 0);
                });
        })->get();

        // Check availability against actual bookings via the service
        foreach ($staff as $person) {
            try {
                $this->availabilityService->verifySlotAvailability(
                    $locationId, $person->id, $serviceId, $date, $startTime, $endTime
                );
                return $person->id;
            } catch (\Exception $e) {
                continue; // Staff is booked, check next
            }
        }

        throw new \Exception("No staff members are available for the selected time slot.");
    }

    /**
     * API: Fetch staff who can perform the SINGLE selected service.
     */
    public function getCapableStaff(Request $request)
    {
        $locationId = $request->query('location_id');
        $date = $request->query('date');
        $serviceId = $request->query('service_id');

        if (!$locationId || !$date || !$serviceId) {
            return response()->json([]);
        }

        $dayOfWeek = strtolower(Carbon::parse($date)->shortEnglishDayOfWeek);

        $staff = User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['owner', 'manager', 'staff']);
        })->whereHas('staff', function($q) use ($locationId, $dayOfWeek, $date, $serviceId) {
            $q->whereHas('locations', fn($l) => $l->where('locations.id', $locationId))
                ->whereHas('workingHours', fn($w) => $w->where('day_of_week', $dayOfWeek)->where('is_day_off', 0))
                ->whereDoesntHave('blackoutDates', fn($b) => $b->where('date', $date))
                ->whereHas('services', fn($s) => $s->where('services.id', $serviceId));
        })->select('id', 'name')->get();

        return response()->json($staff);
    }
}
