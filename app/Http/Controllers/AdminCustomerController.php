<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->where('role', 'customer');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query
            ->withCount('bookings')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $totalCustomers = User::where('role', 'customer')->count();

        $newCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->count();

        $verifiedCustomers = User::where('role', 'customer')
            ->whereNotNull('email_verified_at')
            ->count();

        return view('admin.customers.index', compact(
            'customers',
            'totalCustomers',
            'newCustomers',
            'verifiedCustomers'
        ));
    }

    public function show(User $user)
    {
        abort_unless($user->role === 'customer', 404);

        $bookings = Booking::with([
                'flight.departureAirport',
                'flight.arrivalAirport',
            ])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'bookings_page');

        $totalBookings = Booking::where('user_id', $user->id)
            ->count();

        $totalPaid = Booking::where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('total_price');

        return view('admin.customers.show', compact(
            'user',
            'bookings',
            'totalBookings',
            'totalPaid'
        ));
    }
}
