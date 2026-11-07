<?php

namespace Tests\Feature;

use App\Models\Airport;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'password' => Hash::make('Password123'),
        ]);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertUnauthorized();
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = $this->createUserWithRole('customer');

        $this->actingAs($customer)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_staff_cannot_access_admin_dashboard(): void
    {
        $staff = $this->createUserWithRole('staff');

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Trung tâm quản trị')
            ->assertSee('Quản lý sân bay')
            ->assertSee('Quản lý chuyến bay')
            ->assertSee('Quản lý hóa đơn')
            ->assertSee('Quản lý hoàn tiền');
    }

    public function test_admin_login_redirects_to_dashboard(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->post(route('login.submit'), [
            'email' => $admin->email,
            'password' => 'Password123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_customer_login_redirects_to_home(): void
    {
        $customer = $this->createUserWithRole('customer');

        $this->post(route('login.submit'), [
            'email' => $customer->email,
            'password' => 'Password123',
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($customer);
    }

    public function test_staff_login_redirects_to_home(): void
    {
        $staff = $this->createUserWithRole('staff');

        $this->post(route('login.submit'), [
            'email' => $staff->email,
            'password' => 'Password123',
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($staff);
    }

    public function test_dashboard_displays_correct_statistics(): void
    {
        $admin = $this->createUserWithRole('admin');

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

        $flight = Flight::create([
            'flight_number' => 'SB100',
            'departure_airport_id' => $departure->id,
            'arrival_airport_id' => $arrival->id,
            'departure_time' => now()->addDays(5),
            'arrival_time' => now()->addDays(5)->addHours(2),
            'price' => 1500000,
            'available_seats' => 50,
            'status' => 'scheduled',
        ]);

        Booking::create([
            'flight_id' => $flight->id,
            'pnr_code' => 'DB1234',
            'passenger_name' => 'Nguyen Van A',
            'passenger_email' => 'dashboard@example.com',
            'passenger_phone' => '0912345678',
            'passenger_count' => 1,
            'total_price' => 1500000,
            'status' => 'paid',
        ]);

        Booking::create([
            'flight_id' => $flight->id,
            'pnr_code' => 'DB5678',
            'passenger_name' => 'Nguyen Van B',
            'passenger_email' => 'pending@example.com',
            'passenger_phone' => '0912345679',
            'passenger_count' => 1,
            'total_price' => 2000000,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('totalAirports', 2)
            ->assertViewHas('totalFlights', 1)
            ->assertViewHas('totalBookings', 2)
            ->assertViewHas('paidBookings', 1)
            ->assertViewHas('pendingBookings', 1)
            ->assertViewHas('totalRevenue', function ($revenue) {
                return (float) $revenue === 1500000.0;
            })
            ->assertSee('DB1234')
            ->assertSee('DB5678');
    }
}
