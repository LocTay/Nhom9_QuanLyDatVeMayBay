<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'airline_id',
        'departure_airport_id',
        'arrival_airport_id',
        'departure_time',
        'arrival_time',
        'price',
        'available_seats',
        'status',
    ];

    // Quan hệ với sân bay đi (Departure Airport)
    public function departureAirport()
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    // Quan hệ với sân bay đến (Arrival Airport)
    public function arrivalAirport()
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_id');
    }

    // Một chuyến bay có thể có nhiều đơn đặt vé
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }
}
