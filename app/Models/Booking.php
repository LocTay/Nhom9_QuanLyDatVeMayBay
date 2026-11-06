<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'pnr_code',
    'booking_code',
    'flight_id',
    'passenger_name',
    'passenger_email',
    'passenger_phone',
    'passenger_gender',
    'passenger_count',
    'number_of_seats',
    'passengers_data',
    'total_price',
    'status',
    'trip_type',
    'flight_class',
    'payment_method',
    'payment_time',
    'refund_status',
    'cancellation_reason',
];

    protected $casts = [
        'passengers_data' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            // Đồng bộ hai mã đặt vé
            $code = $booking->booking_code
                ?: $booking->pnr_code
                ?: strtoupper(Str::random(6));

            $booking->booking_code = $code;
            $booking->pnr_code = $code;

            // Đồng bộ số lượng hành khách và số ghế
            $count = $booking->passenger_count
                ?? $booking->number_of_seats
                ?? 1;

            $booking->passenger_count = $count;
            $booking->number_of_seats = $count;
        });
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}
