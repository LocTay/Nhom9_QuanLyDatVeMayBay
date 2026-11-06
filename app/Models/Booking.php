<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
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
    ];
  protected $table = 'bookings';

    protected $casts = [
        'passengers_data' => 'array', // Tự động decode dữ liệu JSON hành khách
        'payment_time' => 'datetime',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}