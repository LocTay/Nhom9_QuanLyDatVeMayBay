<?php

namespace Tests\Feature;

use App\Models\Airport;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAirportTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $admin = User::factory()->create();

        // Chỉ thiết lập role trong dữ liệu kiểm thử.
        $admin->forceFill(['role' => 'admin'])->save();

        return $admin;
    }

    public function test_guest_cannot_access_airport_management(): void
    {
        $this->get(route('admin.airports.index'))
            ->assertUnauthorized();
    }

    public function test_customer_cannot_access_airport_management(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('admin.airports.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_airport_list(): void
    {
        $this->actingAs($this->createAdmin())
            ->get(route('admin.airports.index'))
            ->assertOk();
    }

    public function test_admin_can_create_airport(): void
    {
        $this->actingAs($this->createAdmin())
            ->post(route('admin.airports.store'), [
                'code' => 'abc',
                'name' => 'San bay Moi',
                'city' => 'Da Lat',
                'country' => 'Viet Nam',
            ])
            ->assertRedirect(route('admin.airports.index'));

        $this->assertDatabaseHas('airports', [
            'code' => 'ABC',
            'name' => 'San bay Moi',
            'city' => 'Da Lat',
        ]);
    }

    public function test_admin_cannot_create_airport_with_duplicate_code(): void
    {
        Airport::create([
            'code' => 'SGN',
            'name' => 'Tan Son Nhat',
            'city' => 'Ho Chi Minh',
            'country' => 'Viet Nam',
        ]);

        $this->actingAs($this->createAdmin())
            ->post(route('admin.airports.store'), [
                'code' => 'SGN',
                'name' => 'Another Airport',
                'city' => 'Another City',
                'country' => 'Viet Nam',
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_admin_can_update_airport(): void
    {
        $airport = Airport::create([
            'code' => 'DAD',
            'name' => 'Da Nang Airport',
            'city' => 'Da Nang',
            'country' => 'Viet Nam',
        ]);

        $this->actingAs($this->createAdmin())
            ->put(route('admin.airports.update', $airport), [
                'code' => 'DLI',
                'name' => 'Lien Khuong Airport',
                'city' => 'Da Lat',
                'country' => 'Viet Nam',
            ])
            ->assertRedirect(route('admin.airports.index'));

        $this->assertDatabaseHas('airports', [
            'id' => $airport->id,
            'code' => 'DLI',
            'city' => 'Da Lat',
        ]);
    }

    public function test_admin_cannot_delete_airport_used_by_flight(): void
    {
        $departure = Airport::create([
            'code' => 'SGN',
            'name' => 'Tan Son Nhat',
            'city' => 'Ho Chi Minh',
            'country' => 'Viet Nam',
        ]);

        $arrival = Airport::create([
            'code' => 'HAN',
            'name' => 'Noi Bai',
            'city' => 'Ha Noi',
            'country' => 'Viet Nam',
        ]);

        Flight::create([
            'flight_number' => 'VN001',
            'departure_airport_id' => $departure->id,
            'arrival_airport_id' => $arrival->id,
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDays(1)->addHours(2),
            'price' => 1000000,
            'available_seats' => 100,
            'status' => 'scheduled',
        ]);

        $this->actingAs($this->createAdmin())
            ->delete(route('admin.airports.destroy', $departure))
            ->assertRedirect(route('admin.airports.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('airports', [
            'id' => $departure->id,
        ]);
    }

    public function test_admin_can_delete_airport_not_used_by_flight(): void
    {
        $airport = Airport::create([
            'code' => 'VCA',
            'name' => 'Can Tho Airport',
            'city' => 'Can Tho',
            'country' => 'Viet Nam',
        ]);

        $this->actingAs($this->createAdmin())
            ->delete(route('admin.airports.destroy', $airport))
            ->assertRedirect(route('admin.airports.index'));

        $this->assertDatabaseMissing('airports', [
            'id' => $airport->id,
        ]);
    }
}
