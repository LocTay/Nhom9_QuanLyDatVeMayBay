<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'flight_id',
        'user_id',
        'passenger_name',
        'passenger_email',
        'passenger_phone',
        'number_of_seats',
        'total_price',
        'status',
        'payment_time',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}