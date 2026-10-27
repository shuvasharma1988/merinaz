<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard homepage.
     */
    public function index(Request $request): View
    {
        // Placeholder metrics based on the Merinaz specification
        $metrics = [
            'bookings_today' => 14,
            'checked_in' => 6,
            'revenue_estimate' => 1840,
            'new_reviews' => 3,
        ];
        // Placeholder schedule data
        $schedule = [
            ['time' => '10:00 AM', 'client' => 'Mei Lin', 'service' => 'Brazilian', 'staff' => 'Leyla', 'status' => 'Checked In'],
            ['time' => '12:30 PM', 'client' => 'Hana', 'service' => 'Brow shaping', 'staff' => 'Nadia', 'status' => 'Confirmed'],
            ['time' => '02:00 PM', 'client' => 'Walk-in', 'service' => 'Upper lip', 'staff' => 'Priya', 'status' => 'Pending'],
        ];

        return view('admin.dashboard', compact('metrics', 'schedule'));
    }
}
