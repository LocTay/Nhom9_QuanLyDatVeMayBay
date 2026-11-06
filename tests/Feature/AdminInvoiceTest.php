<?php

namespace Tests\Feature;

use App\Models\Airport;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function createBooking(array $attributes = []): Booking
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

        $flight = Flight::create([
            'flight_number' => 'SB999',
            'departure_airport_id' => $departure->id,
            'arrival_airport_id' => $arrival->id,
            'departure_time' => now()->addDays(5),
            'arrival_time' => now()->addDays(5)->addHours(2),
            'price' => 1500000,
            'available_seats' => 100,
            'status' => 'scheduled',
        ]);

        return Booking::create(array_merge([
            'flight_id' => $flight->id,
            'pnr_code' => 'ABC123',
            'passenger_name' => 'Nguyen Van A',
            'passenger_email' => 'nguyenvana@example.com',
            'passenger_phone' => '0901234567',
            'passenger_count' => 1,
            'total_price' => 1500000,
            'status' => 'paid',
        ], $attributes));
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_guest_cannot_access_invoice_management(): void
    {
        $this->get(route('admin.invoices.index'))
            ->assertUnauthorized();
    }

    public function test_customer_cannot_access_invoice_management(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('admin.invoices.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_invoice_list(): void
    {
        $this->createBooking();

        $this->actingAs($this->admin())
            ->get(route('admin.invoices.index'))
            ->assertOk()
            ->assertSee('ABC123')
            ->assertSee('Nguyen Van A');
    }

    public function test_admin_can_search_invoice_by_booking_code(): void
    {
        $this->createBooking();

        $this->actingAs($this->admin())
            ->get(route('admin.invoices.index', [
                'search' => 'ABC123',
            ]))
            ->assertOk()
            ->assertSee('ABC123');
    }

    public function test_admin_can_filter_paid_invoices(): void
    {
        $this->createBooking(['status' => 'paid']);

        $this->actingAs($this->admin())
            ->get(route('admin.invoices.index', [
                'status' => 'paid',
            ]))
            ->assertOk()
            ->assertSee('Nguyen Van A');
    }

    public function test_admin_can_view_invoice_detail(): void
    {
        $booking = $this->createBooking();

        $this->actingAs($this->admin())
            ->get(route('admin.invoices.show', $booking->id))
            ->assertOk()
            ->assertSee('ABC123')
            ->assertSee('1.500.000');
    }

    public function test_customer_cannot_view_invoice_detail(): void
    {
        $booking = $this->createBooking();

        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('admin.invoices.show', $booking->id))
            ->assertForbidden();
    }

    public function test_admin_can_search_legacy_booking_code(): void
    {
        $booking = $this->createBooking();

        $booking->update([
            'pnr_code' => null,
            'booking_code' => 'VN101',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.invoices.index', [
                'search' => 'VN101',
            ]))
            ->assertOk()
            ->assertSee('VN101');
    }

    public function test_paid_filter_excludes_pending_booking(): void
    {
        $paid = $this->createBooking([
            'status' => 'paid',
        ]);

        Booking::create([
            'flight_id' => $paid->flight_id,
            'pnr_code' => 'XYZ789',
            'passenger_name' => 'Khach Hang Cho',
            'passenger_email' => 'pending@example.com',
            'passenger_phone' => '0909876543',
            'passenger_count' => 1,
            'total_price' => 900000,
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.invoices.index', [
                'status' => 'paid',
            ]))
            ->assertOk()
            ->assertSee('ABC123')
            ->assertDontSee('XYZ789');
    }

    public function test_revenue_only_counts_paid_bookings(): void
    {
        $paid = $this->createBooking([
            'status' => 'paid',
            'total_price' => 1500000,
        ]);

        Booking::create([
            'flight_id' => $paid->flight_id,
            'pnr_code' => 'XYZ789',
            'passenger_name' => 'Khach Hang B',
            'passenger_email' => 'customerb@example.com',
            'passenger_phone' => '0909876543',
            'passenger_count' => 1,
            'total_price' => 900000,
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.invoices.index'))
            ->assertOk()
            ->assertViewHas('totalRevenue', function ($revenue) {
                return (float) $revenue === 1500000.0;
            });
    }
}
