<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreBookingRequest;
use App\Http\Requests\Backend\UpdateBookingRequest;
use App\Http\Requests\Backend\UpdateBookingStatusRequest;
use App\Models\Appointment;
use App\Models\Location;
use App\Models\Service;
use App\Models\User;
use App\Services\Backend\BookingService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
// Add these methods to your existing BookingController
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

 // Assuming this model exists based on the DB table

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function index(): View
    {
        Gate::authorize('viewAny', Appointment::class);

        $bookings = Appointment::with(['client', 'location', 'items.service', 'items.staff'])
            ->when(! auth()->user()->roles->contains('slug', 'manager') && ! auth()->user()->roles->contains('slug', 'owner'), function ($query) {
                // If a standard staff member is logged in, only show visits where they are assigned to at least one item
                $query->whereHas('items', function ($itemQuery) {
                    $itemQuery->where('staff_id', auth()->id());
                });
            })
            ->orderBy('appointment_date', 'desc')
            ->paginate(15);

        return view('admin.booking.index', compact('bookings'));
    }

    public function create(): View
    {
        Gate::authorize('create', Appointment::class);
        $locations = Location::where('is_active', true)->get();
        $clients = User::whereHas('roles', fn ($q) => $q->where('slug', 'customer'))->get();
        $services = Service::where('is_active', true)->get();
        $staffList = User::whereHas('roles', fn ($q) => $q->whereIn('slug', ['owner', 'manager', 'staff']))->get();

        return view('admin.booking.create', compact('clients', 'locations', 'services', 'staffList'));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        try {
            $this->bookingService->createBooking($request->validated());

            return redirect()->route('admin.bookings.index')->with('success', 'Booking created successfully.');
        } catch (Exception $e) {
            // This catches the availability overlap exception and redirects back with the error
            return back()->withInput()->withErrors(['appointment_date' => $e->getMessage()]);
        }
    }

    public function show(Appointment $booking): View
    {
        Gate::authorize('view', $booking);

        // FIXED: Load location and nested items (which contain the staff and service)
        $booking->load(['client', 'location', 'items.service', 'items.staff']);

        return view('admin.booking.details', compact('booking'));
    }

    public function edit(Appointment $booking): View
    {
        Gate::authorize('update', $booking);

        // 1. Load the nested multi-service items
        $booking->load(['client', 'location', 'items.service', 'items.staff']);

        // 2. Fetch ALL locations and services (Removed is_active check for testing)
        $locations = Location::all();
        $services = Service::all();

        // DIAGNOSTIC STOP: If they are still empty, this will tell us immediately!
        if ($locations->isEmpty() || $services->isEmpty()) {
            dd('Database Error: Locations or Services tables are returning 0 records.', 'Locations Count: '.$locations->count(), 'Services Count: '.$services->count());
        }

        return view('admin.booking.edit', compact('booking', 'locations', 'services'));
    }

    public function update(UpdateBookingRequest $request, Appointment $booking): RedirectResponse
    {
        // Note: Authorization for update is handled inside UpdateBookingRequest's authorize() method
        try {
            $this->bookingService->updateBooking($booking, $request->validated());

            return redirect()->route('admin.bookings.index')->with('success', 'Booking updated successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Appointment $booking): RedirectResponse
    {
        Gate::authorize('delete', $booking);

        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully.');
    }

    public function updateStatus(UpdateBookingStatusRequest $request, Appointment $booking): RedirectResponse
    {
        // Note: Authorization is handled inside UpdateBookingStatusRequest's authorize() method
        $this->bookingService->updateStatus($booking, $request->status);

        return back()->with('success', 'Booking status updated successfully.');
    }

    public function searchClients(Request $request): JsonResponse
    {
        Gate::authorize('create', Appointment::class);

        $query = $request->get('q');
        $clients = User::whereHas('roles', fn ($q) => $q->where('slug', 'customer'))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'email')
            ->take(10)
            ->get();

        return response()->json($clients);
    }

    public function getStaffByLocation(Request $request, int $locationId): JsonResponse
    {
        Gate::authorize('create', Appointment::class);
        $date = $request->query('date', now()->format('Y-m-d'));

        // Define only ONE date variable matching the 3-letter database format (e.g., 'wed')
        $dayOfWeek = strtolower(Carbon::parse($date)->shortEnglishDayOfWeek);

        $staff = User::select('id', 'name')
            ->whereHas('staff', function ($profileQuery) use ($locationId, $dayOfWeek) {

                // 1. Verify location assignment via pivot table
                $profileQuery->whereHas('locations', function ($locationQuery) use ($locationId) {
                    $locationQuery->where('locations.id', $locationId);
                });

                // 2. Verify they have active working hours today using the $dayOfWeek variable
                $profileQuery->whereHas('workingHours', function ($hoursQuery) use ($dayOfWeek) {
                    $hoursQuery->where('day_of_week', $dayOfWeek)
                        ->where('is_day_off', 0);
                });

            })
            ->get();

        return response()->json($staff);
    }
}
