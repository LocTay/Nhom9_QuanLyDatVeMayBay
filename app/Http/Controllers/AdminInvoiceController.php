<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class AdminInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with([
            'flight.departureAirport',
            'flight.arrivalAirport',
        ]);

        // Tìm kiếm theo mã vé, tên hoặc email khách hàng
        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('pnr_code', 'like', "%{$search}%")
                    ->orWhere('booking_code', 'like', "%{$search}%")
                    ->orWhere('passenger_name', 'like', "%{$search}%")
                    ->orWhere('passenger_email', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $bookings = $query
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $totalBookings = Booking::count();

        $paidBookings = Booking::where('status', 'paid')->count();

        $pendingBookings = Booking::where('status', 'pending')->count();

        $totalRevenue = Booking::where('status', 'paid')
            ->sum('total_price');
        $pendingApprovalBookings = Booking::where('status', 'pending_approval')->count();
        return view('admin.invoices.index', compact(
            'bookings',
            'totalBookings',
            'paidBookings',
            'pendingBookings',
            'pendingApprovalBookings',
            'totalRevenue'
        ));
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'flight.departureAirport',
            'flight.arrivalAirport',
        ]);

        return view('admin.invoices.show', compact('booking'));
    }
}
