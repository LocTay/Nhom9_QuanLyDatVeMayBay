<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Flight;
use App\Models\Booking;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalAirports = Airport::count();

        $totalFlights = Flight::count();

        $totalBookings = Booking::count();

        $totalCustomers = User::where('role', 'customer')->count();

        $paidBookings = Booking::where('status', 'paid')->count();

        $pendingBookings = Booking::where('status', 'pending')->count();

        $pendingRefunds = Booking::where(
            'refund_status',
            'pending_refund'
        )->count();

        $totalRevenue = Booking::where('status', 'paid')
            ->sum('total_price');

        $recentBookings = Booking::with([
            'flight.departureAirport',
            'flight.arrivalAirport'
        ])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalAirports',
            'totalFlights',
            'totalBookings',
            'totalCustomers',
            'paidBookings',
            'pendingBookings',
            'pendingRefunds',
            'totalRevenue',
            'recentBookings'
        ));
    }
}
