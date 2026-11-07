<?php

namespace Tests\Feature;

use App\Models\Airport;
use App\Models\Flight;
use App\Models\Airline;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAirlineTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }


    public function test_admin_cannot_delete_airline_used_by_flight(): void
    {
        $airline = Airline::create([
            'code' => 'VN',
            'name' => 'Vietnam Airlines',
            'is_active' => true,
        ]);

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

        Flight::create([
            'flight_number' => 'VN123',
            'airline_id' => $airline->id,
            'departure_airport_id' => $departure->id,
            'arrival_airport_id' => $arrival->id,
            'departure_time' => now()->addDays(5),
            'arrival_time' => now()->addDays(5)->addHours(2),
            'price' => 1500000,
            'available_seats' => 20,
            'status' => 'scheduled',
        ]);

        $this->actingAs($this->createAdmin())
            ->delete(route('admin.airlines.destroy', $airline))
            ->assertRedirect(route('admin.airlines.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('airlines', [
            'id' => $airline->id,
            'code' => 'VN',
        ]);
    }

    public function test_guest_cannot_access_airline_management(): void
    {
        $this->get(route('admin.airlines.index'))
            ->assertUnauthorized();
    }

    public function test_customer_cannot_access_airline_management(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($customer)
            ->get(route('admin.airlines.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_airline_list(): void
    {
        Airline::create([
            'code' => 'VN',
            'name' => 'Vietnam Airlines',
            'country' => 'Việt Nam',
            'is_active' => true,
        ]);

        $this->actingAs($this->createAdmin())
            ->get(route('admin.airlines.index'))
            ->assertOk()
            ->assertSee('Vietnam Airlines');
    }

    public function test_admin_can_create_airline(): void
    {
        $response = $this->actingAs($this->createAdmin())
            ->post(route('admin.airlines.store'), [
                'code' => 'vj',
                'name' => 'Vietjet Air',
                'country' => 'Việt Nam',
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.airlines.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('airlines', [
            'code' => 'VJ',
            'name' => 'Vietjet Air',
            'is_active' => 1,
        ]);
    }

    public function test_admin_cannot_create_duplicate_airline_code(): void
    {
        Airline::create([
            'code' => 'VN',
            'name' => 'Vietnam Airlines',
            'is_active' => true,
        ]);

        $this->actingAs($this->createAdmin())
            ->post(route('admin.airlines.store'), [
                'code' => 'vn',
                'name' => 'Another Airline',
                'is_active' => 1,
            ])
            ->assertSessionHasErrors('code');

        $this->assertEquals(1, Airline::count());
    }

    public function test_admin_can_update_airline(): void
    {
        $airline = Airline::create([
            'code' => 'VN',
            'name' => 'Vietnam Airlines',
            'country' => 'Việt Nam',
            'is_active' => true,
        ]);

        $this->actingAs($this->createAdmin())
            ->put(route('admin.airlines.update', $airline), [
                'code' => 'VN',
                'name' => 'Vietnam Airlines Updated',
                'country' => 'Việt Nam',
                'is_active' => 0,
            ])
            ->assertRedirect(route('admin.airlines.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('airlines', [
            'id' => $airline->id,
            'name' => 'Vietnam Airlines Updated',
            'is_active' => 0,
        ]);
    }

    public function test_admin_can_search_airline(): void
    {
        Airline::create([
            'code' => 'VN',
            'name' => 'Vietnam Airlines',
            'is_active' => true,
        ]);

        Airline::create([
            'code' => 'VJ',
            'name' => 'Vietjet Air',
            'is_active' => true,
        ]);

        $this->actingAs($this->createAdmin())
            ->get(route('admin.airlines.index', [
                'search' => 'Vietnam',
            ]))
            ->assertOk()
            ->assertSee('Vietnam Airlines')
            ->assertDontSee('Vietjet Air');
    }

    public function test_customer_cannot_create_airline(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($customer)
            ->post(route('admin.airlines.store'), [
                'code' => 'VN',
                'name' => 'Vietnam Airlines',
                'is_active' => 1,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('airlines', 0);
    }


    public function test_admin_can_delete_airline_without_flights(): void
    {
        $airline = Airline::create([
            'code' => 'VN',
            'name' => 'Vietnam Airlines',
            'is_active' => true,
        ]);

        $this->actingAs($this->createAdmin())
            ->delete(route('admin.airlines.destroy', $airline))
            ->assertRedirect(route('admin.airlines.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('airlines', [
            'id' => $airline->id,
        ]);
    }

    public function test_customer_cannot_delete_airline(): void
    {
        $airline = Airline::create([
            'code' => 'VN',
            'name' => 'Vietnam Airlines',
            'is_active' => true,
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($customer)
            ->delete(route('admin.airlines.destroy', $airline))
            ->assertForbidden();

        $this->assertDatabaseHas('airlines', [
            'id' => $airline->id,
        ]);
    }
}
