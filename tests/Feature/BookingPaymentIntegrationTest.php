<?php

namespace Tests\Feature;

use App\Models\Airport;
use App\Models\Booking;
use App\Models\Flight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingPaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function createFlight(int $seats = 20): Flight
    {
        $departure = Airport::create([
            'code' => 'SGN',
            'name' => 'Tân Sơn Nhất',
            'city' => 'TP. Hồ Chí Minh',
            'country' => 'Việt Nam',
        ]);

        $arrival = Airport::create([
            'code' => 'HAN',
            'name' => 'Nội Bài',
            'city' => 'Hà Nội',
            'country' => 'Việt Nam',
        ]);

        return Flight::create([
            'flight_number' => 'VN123',
            'departure_airport_id' => $departure->id,
            'arrival_airport_id' => $arrival->id,
            'departure_time' => now()->addDays(5),
            'arrival_time' => now()->addDays(5)->addHours(2),
            'price' => 1500000,
            'available_seats' => $seats,
            'status' => 'scheduled',
        ]);
    }

    private function bookingData(Flight $flight, int $count = 2): array
    {
        $passengers = [];

        for ($i = 0; $i < $count; $i++) {
            $passengers[] = [
                'name' => 'Hanh khach ' . ($i + 1),
                'gender' => 'Nam',
                'seat' => 'C' . ($i + 1),
            ];
        }

        return [
            'flight_id' => $flight->id,
            'passenger_name' => 'Nguyen Van A',
            'passenger_email' => 'test@example.com',
            'passenger_phone' => '0912345678',
            'passenger_gender' => 'Nam',
            'passenger_count' => $count,
            'trip_type' => 'one_way',
            'flight_class' => 'pho_thong',
            'passengers' => $passengers,
        ];
    }

    public function test_booking_creates_pnr_and_decreases_seats(): void
    {
        $flight = $this->createFlight(20);

        $response = $this->post(
            route('booking.store'),
            $this->bookingData($flight, 2)
        );

        $booking = Booking::first();

        $this->assertNotNull($booking);

        $response->assertRedirect(
            route('booking.success', ['id' => $booking->id])
        );

        $this->assertNotEmpty($booking->pnr_code);
        $this->assertEquals($booking->pnr_code, $booking->booking_code);
        $this->assertEquals(2, $booking->passenger_count);
        $this->assertEquals(2, $booking->number_of_seats);
        $this->assertEquals(18, $flight->fresh()->available_seats);
    }

    public function test_customer_can_lookup_booking_by_pnr(): void
    {
        $flight = $this->createFlight();

        $this->post(
            route('booking.store'),
            $this->bookingData($flight)
        );

        $booking = Booking::firstOrFail();

        $response = $this->post(route('ticket.lookup'), [
            'booking_code' => $booking->pnr_code,
        ]);

        $response->assertRedirect(
            route('ticket.detail', [
                'booking_code' => $booking->pnr_code,
            ])
        );

        $this->get(route('ticket.detail', [
            'booking_code' => $booking->pnr_code,
        ]))->assertOk();
    }

    public function test_customer_can_pay_for_booking(): void
    {
        $flight = $this->createFlight();

        $this->post(
            route('booking.store'),
            $this->bookingData($flight)
        );

        $booking = Booking::firstOrFail();

        $response = $this->post(
            route('ticket.payment', [
                'booking_code' => $booking->pnr_code,
            ]),
            ['payment_status' => 'success']
        );

        $response->assertSessionHas('success');

        $booking->refresh();

        $this->assertEquals('paid', $booking->status);
        $this->assertNotNull($booking->payment_time);
    }

    public function test_cancel_booking_restores_available_seats(): void
    {
        $flight = $this->createFlight(20);

        $this->post(
            route('booking.store'),
            $this->bookingData($flight, 2)
        );

        $booking = Booking::firstOrFail();

        $this->assertEquals(18, $flight->fresh()->available_seats);

        $response = $this->post(route('ticket.cancel', [
            'booking_code' => $booking->pnr_code,
        ]));

        $response->assertSessionHas('success');

        $this->assertEquals('cancelled', $booking->fresh()->status);
        $this->assertEquals(20, $flight->fresh()->available_seats);
    }

    public function test_booking_fails_when_not_enough_seats(): void
    {
        $flight = $this->createFlight(1);

        $response = $this->from(
            route('booking.create', ['flight_id' => $flight->id])
        )->post(
            route('booking.store'),
            $this->bookingData($flight, 2)
        );

        $response->assertRedirect();
        $response->assertSessionHasErrors('passenger_count');

        $this->assertDatabaseCount('bookings', 0);
        $this->assertEquals(1, $flight->fresh()->available_seats);
    }
}
