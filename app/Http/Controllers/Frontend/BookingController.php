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

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected AvailabilityService $availabilityService
    ) {}

    /**
     * Step 1: Display location and multi-service selection.
     */
    public function index(Request $request)
    {
        $locations = Location::where('is_active', true)->get();
        $services = Service::where('is_active', true)->orderBy('category_id')->get();

        // Fetch users who have the role 'owner', 'manager', or 'staff'
        $staffList = User::whereHas('roles', fn($q) => $q->whereIn('slug', ['owner', 'manager', 'staff']))->get();

        $selectedLocation = $request->query('location') ?? $locations->first()->id ?? null;

        return view('frontend.booking.index', compact('locations', 'services', 'staffList', 'selectedLocation'));
    }

    /**
     * API: Fetch dynamic available slots for the selected date and services.
     */
    public function getAvailableSlots(Request $request)
    {
        $locationId = $request->query('location_id');
        $date = $request->query('date');
        $serviceIds = $request->query('services', []);

        if (!$locationId || !$date || empty($serviceIds)) {
            return response()->json([]);
        }

        // Generate baseline operating slots. In a fully mature system, this loop
        // would ping your AvailabilityService for every 20-min block to ensure capacity.
        $operatingBlocks = ['10:00', '10:40', '11:20', '12:00', '12:40', '13:30', '14:10', '14:50', '15:40', '16:20', '17:00', '17:40'];

        $availableSlots = [];
        foreach ($operatingBlocks as $time) {
            $availableSlots[] = [
                'time' => $time,
                'label' => Carbon::parse($time)->format('g:i A') // e.g., "10:00 AM"
            ];
        }

        return response()->json($availableSlots);
    }

    /**
     * Step 2: Validate Step 1 payload and display customer details form.
     */
    public function details(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'staff_id' => 'nullable|exists:users,id'
        ]);

        $selectedServices = Service::whereIn('id', $validated['services'])->get();
        $location = Location::findOrFail($validated['location_id']);

        return view('frontend.booking.details', compact('validated', 'selectedServices', 'location'));
    }

    /**
     * Step 3: Process the booking, handle guest accounts, and pass to backend BookingService.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'staff_id' => 'nullable|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'customer_notes' => 'nullable|string|max:1000',
            'terms_consent' => 'accepted',

        ]);

        // 1. Resolve User (Authenticated vs Guest Checkout)
        $user = auth()->user();

        if (!$user) {
            $user = User::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'password' => Hash::make(Str::random(16)), // Secure random password for guest
                ]
            );

            // Assign Customer Role
            $customerRole = Role::where('slug', 'customer')->first();
            if ($customerRole) {
                $user->roles()->syncWithoutDetaching([$customerRole->id]);
            }

            // Sync with CRM ClientProfile
            $user->clientProfile()->firstOrCreate(
                ['user_id' => $user->id],
                ['phone' => $validated['phone']]
            );
        }

        // 2. Map Multi-Service payload for the backend BookingService
        $items = [];
        $startTime = $validated['time'];

        foreach ($validated['services'] as $serviceId) {
            $service = Service::findOrFail($serviceId);
            $duration = (int) ($service->duration ?? $service->duration_minutes ?? 30);

            // If no specific staff is requested, auto-assign the first available
            $assignedStaffId = $validated['staff_id'] ?? $this->findFirstAvailableStaff(
                $validated['location_id'],
                $service->id,
                $validated['date'],
                $startTime,
                $duration
            );

            $items[] = [
                'service_id' => $service->id,
                'staff_id' => $assignedStaffId,
                'start_time' => $startTime,
            ];

            // Increment timeline for next queued service
            $startTime = Carbon::parse($startTime)->addMinutes($duration)->format('H:i');
        }

        // 3. Format payload to strict backend limits
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

        try {
            // 4. Hit Backend Transaction
            $appointment = $this->bookingService->createBooking($bookingData);
            return redirect()->route('booking.confirmation', $appointment->booking_reference);

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
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
}
