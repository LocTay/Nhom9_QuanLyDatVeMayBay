<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRefundTest extends TestCase
{
    use RefreshDatabase;

    private function createBooking(array $attributes = []): Booking
    {
        $airport1 = \App\Models\Airport::create([
            'code' => 'SGN',
            'name' => 'Tan Son Nhat',
            'city' => 'Ho Chi Minh',
            'country' => 'Viet Nam',
        ]);

        $airport2 = \App\Models\Airport::create([
            'code' => 'HAN',
            'name' => 'Noi Bai',
            'city' => 'Ha Noi',
            'country' => 'Viet Nam',
        ]);

        $flight = \App\Models\Flight::create([
            'flight_number' => 'RF100',
            'departure_airport_id' => $airport1->id,
            'arrival_airport_id' => $airport2->id,
            'departure_time' => now()->addDays(5),
            'arrival_time' => now()->addDays(5)->addHours(2),
            'price' => 1500000,
            'available_seats' => 50,
            'status' => 'scheduled',
        ]);

        return Booking::create(array_merge([
            'flight_id' => $flight->id,
            'pnr_code' => 'RF1234',
            'passenger_name' => 'Nguyen Van A',
            'passenger_email' => 'refund@example.com',
            'passenger_phone' => '0901234567',
            'passenger_count' => 1,
            'total_price' => 1500000,
            'status' => 'cancelled',
            'refund_status' => 'pending_refund',
        ], $attributes));
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_guest_cannot_view_refunds(): void
    {
        $this->get(route('admin.refunds.index'))
            ->assertUnauthorized();
    }

    public function test_customer_cannot_view_refunds(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('admin.refunds.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_refund_list(): void
    {
        $this->createBooking();

        $this->actingAs($this->admin())
            ->get(route('admin.refunds.index'))
            ->assertOk()
            ->assertSee('RF1234');
    }

    public function test_admin_can_view_refund_detail(): void
    {
        $booking = $this->createBooking();

        $this->actingAs($this->admin())
            ->get(route('admin.refunds.show', $booking))
            ->assertOk()
            ->assertSee('RF1234')
            ->assertSee('Nguyen Van A');
    }

    public function test_admin_can_complete_pending_refund(): void
    {
        $booking = $this->createBooking();

        $this->actingAs($this->admin())
            ->post(route('admin.refunds.complete', $booking))
            ->assertRedirect(route('admin.refunds.show', $booking));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
            'refund_status' => 'completed',
        ]);
    }

    public function test_refund_cannot_be_completed_twice(): void
    {
        $booking = $this->createBooking();

        $this->actingAs($this->admin())
            ->post(route('admin.refunds.complete', $booking));

        $this->actingAs($this->admin())
            ->post(route('admin.refunds.complete', $booking))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'refund_status' => 'completed',
        ]);
    }

    public function test_admin_cannot_refund_non_cancelled_booking(): void
    {
        $booking = $this->createBooking([
            'status' => 'paid',
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.refunds.complete', $booking))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'refund_status' => 'pending_refund',
        ]);
    }

    public function test_customer_cannot_complete_refund(): void
    {
        $booking = $this->createBooking();

        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->post(route('admin.refunds.complete', $booking))
            ->assertForbidden();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'refund_status' => 'pending_refund',
        ]);
    }

    public function test_refund_workflow_from_pending_to_completed(): void
    {
        $booking = $this->createBooking();

        $admin = $this->admin();

        // Bước 1: Yêu cầu xuất hiện trong danh sách chờ hoàn tiền
        $this->actingAs($admin)
            ->get(route('admin.refunds.index', [
                'refund_status' => 'pending_refund',
            ]))
            ->assertOk()
            ->assertSee('RF1234');

        // Bước 2: Admin xem chi tiết
        $this->actingAs($admin)
            ->get(route('admin.refunds.show', $booking))
            ->assertOk()
            ->assertSee('Chờ hoàn tiền');

        // Bước 3: Admin xác nhận hoàn tiền
        $this->actingAs($admin)
            ->post(route('admin.refunds.complete', $booking))
            ->assertSessionHas('success');

        // Bước 4: Yêu cầu chuyển sang trạng thái hoàn tất
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
            'refund_status' => 'completed',
        ]);

        // Bước 5: Yêu cầu xuất hiện trong danh sách đã hoàn tiền
        $this->actingAs($admin)
            ->get(route('admin.refunds.index', [
                'refund_status' => 'completed',
            ]))
            ->assertOk()
            ->assertSee('RF1234');
    }
}
