<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRefundController extends Controller
{
    // Danh sách yêu cầu hoàn tiền
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:100',
            'refund_status' => 'nullable|in:pending_refund,completed,no_refund',
        ]);

        $query = Booking::with([
            'flight.departureAirport',
            'flight.arrivalAirport'
        ])->whereNotNull('refund_status');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('pnr_code', 'like', "%{$search}%")
                  ->orWhere('booking_code', 'like', "%{$search}%")
                  ->orWhere('passenger_name', 'like', "%{$search}%")
                  ->orWhere('passenger_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('refund_status')) {
            $query->where(
                'refund_status',
                $request->input('refund_status')
            );
        }

        $refunds = $query
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();

        $pendingCount = Booking::where(
            'refund_status',
            'pending_refund'
        )->count();

        $completedCount = Booking::where(
            'refund_status',
            'completed'
        )->count();

        $pendingAmount = Booking::where(
            'refund_status',
            'pending_refund'
        )->sum('total_price');

        return view('admin.refunds.index', compact(
            'refunds',
            'pendingCount',
            'completedCount',
            'pendingAmount'
        ));
    }

    // Chi tiết yêu cầu hoàn tiền
    public function show(Booking $booking)
    {
        abort_unless(
            $booking->refund_status !== null,
            404
        );

        $booking->load([
            'flight.departureAirport',
            'flight.arrivalAirport'
        ]);

        return view('admin.refunds.show', compact('booking'));
    }

    // Admin xác nhận đã hoàn tiền (mô phỏng)
    public function complete(Booking $booking)
    {
        $updated = DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Chỉ xử lý vé đã hủy và đang chờ hoàn tiền
            if (
                $lockedBooking->status !== 'cancelled' ||
                $lockedBooking->refund_status !== 'pending_refund'
            ) {
                return false;
            }

            $lockedBooking->refund_status = 'completed';
            $lockedBooking->save();

            return true;
        });

        if (!$updated) {
            return redirect()
                ->route('admin.refunds.show', $booking)
                ->with(
                    'error',
                    'Yêu cầu này không còn đủ điều kiện hoàn tiền.'
                );
        }

        return redirect()
            ->route('admin.refunds.show', $booking)
            ->with(
                'success',
                'Đã cập nhật trạng thái hoàn tiền thành công.'
            );
    }
}
