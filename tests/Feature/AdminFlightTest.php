<?php

namespace Tests\Feature;

use App\Models\Airport;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFlightTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function airports(): array
    {
        $departure = Airport::create([
            'code' => 'SGN',
            'name' => 'Tân Sơn Nhất',
            'city' => 'TP.HCM',
            'country' => 'Việt Nam',
        ]);

        $arrival = Airport::create([
            'code' => 'HAN',
            'name' => 'Nội Bài',
            'city' => 'Hà Nội',
            'country' => 'Việt Nam',
        ]);

        return [$departure, $arrival];
    }

    private function flightData(): array
    {
        [$departure, $arrival] = $this->airports();

        return [
            'flight_number' => 'VN123',
            'departure_airport_id' => $departure->id,
            'arrival_airport_id' => $arrival->id,
            'departure_time' => now()->addDays(5)->setTime(8, 0)->format('Y-m-d H:i:s'),
            'arrival_time' => now()->addDays(5)->setTime(10, 0)->format('Y-m-d H:i:s'),
            'price' => 1500000,
            'available_seats' => 100,
            'status' => 'scheduled',
        ];
    }

    private function flight(): Flight
    {
        return Flight::create($this->flightData());
    }

    private function addBooking(Flight $flight): Booking
    {
        return Booking::create([
            'pnr_code' => 'ABC123',
            'flight_id' => $flight->id,
            'passenger_name' => 'Nguyen Van A',
            'passenger_email' => 'test@example.com',
            'passenger_phone' => '0912345678',
            'passenger_count' => 1,
            'total_price' => 1500000,
            'status' => 'pending',
            'trip_type' => 'one_way',
            'flight_class' => 'pho_thong',
        ]);
    }

    public function test_guest_cannot_access_flight_management(): void
    {
        $this->get(route('admin.flights.index'))
            ->assertUnauthorized();
    }

    public function test_customer_cannot_access_flight_management(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('admin.flights.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_flight_list(): void
    {
        $flight = $this->flight();

        $this->actingAs($this->admin())
            ->get(route('admin.flights.index'))
            ->assertOk()
            ->assertSee($flight->flight_number);
    }

    public function test_admin_can_create_flight(): void
    {
        $data = $this->flightData();

        $this->actingAs($this->admin())
            ->post(route('admin.flights.store'), $data)
            ->assertRedirect(route('admin.flights.index'));

        $this->assertDatabaseHas('flights', [
            'flight_number' => 'VN123',
            'available_seats' => 100,
        ]);
    }

    public function test_admin_cannot_create_flight_with_same_airports(): void
    {
        $data = $this->flightData();

        $data['arrival_airport_id'] = $data['departure_airport_id'];

        $this->actingAs($this->admin())
            ->post(route('admin.flights.store'), $data)
            ->assertSessionHasErrors('departure_airport_id');

        $this->assertDatabaseCount('flights', 0);
    }

    public function test_admin_can_update_flight_without_booking(): void
    {
        $flight = $this->flight();
        $data = $this->flightDataForUpdate($flight);

        $data['price'] = 1800000;

        $this->actingAs($this->admin())
            ->put(route('admin.flights.update', $flight), $data)
            ->assertRedirect(route('admin.flights.index'));

        $this->assertEquals(1800000, $flight->fresh()->price);
    }

    public function test_admin_cannot_change_price_when_flight_has_booking(): void
    {
        $flight = $this->flight();
        $this->addBooking($flight);

        $data = $this->flightDataForUpdate($flight);
        $data['price'] = 1800000;

        $this->actingAs($this->admin())
            ->put(route('admin.flights.update', $flight), $data)
            ->assertSessionHasErrors('price');

        $this->assertEquals(1500000, $flight->fresh()->price);
    }

    public function test_admin_can_change_status_when_flight_has_booking(): void
    {
        $flight = $this->flight();
        $this->addBooking($flight);

        $data = $this->flightDataForUpdate($flight);
        $data['status'] = 'delayed';

        $this->actingAs($this->admin())
            ->put(route('admin.flights.update', $flight), $data)
            ->assertRedirect(route('admin.flights.index'));

        $this->assertEquals('delayed', $flight->fresh()->status);
    }

    public function test_admin_cannot_delete_flight_with_booking(): void
    {
        $flight = $this->flight();
        $this->addBooking($flight);

        $this->actingAs($this->admin())
            ->delete(route('admin.flights.destroy', $flight))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('flights', ['id' => $flight->id]);
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_admin_can_delete_flight_without_booking(): void
    {
        $flight = $this->flight();

        $this->actingAs($this->admin())
            ->delete(route('admin.flights.destroy', $flight))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('flights', ['id' => $flight->id]);
    }

    private function flightDataForUpdate(Flight $flight): array
    {
        return [
            'flight_number' => $flight->flight_number,
            'departure_airport_id' => $flight->departure_airport_id,
            'arrival_airport_id' => $flight->arrival_airport_id,
            'departure_time' => $flight->departure_time,
            'arrival_time' => $flight->arrival_time,
            'price' => $flight->price,
            'available_seats' => $flight->available_seats,
            'status' => $flight->status,
        ];
    }
}
