<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Booking;
use App\Models\Airport;
use App\Models\Flight;

class AdminCustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_customer_management(): void
    {
        $this->get(route('admin.customers.index'))
            ->assertUnauthorized();
    }

    public function test_customer_cannot_access_customer_management(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($customer)
            ->get(route('admin.customers.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_customer_list(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        User::factory()->create([
            'role' => 'customer',
            'name' => 'Nguyen Van Khach',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee('Quản lý khách hàng')
            ->assertSee('Nguyen Van Khach');
    }

    public function test_admin_can_search_customer_by_email(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        User::factory()->create([
            'role' => 'customer',
            'email' => 'khachhang@example.com',
        ]);

        User::factory()->create([
            'role' => 'customer',
            'email' => 'nguoidung@example.com',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.customers.index', [
                'search' => 'khachhang@example.com',
            ]))
            ->assertOk()
            ->assertSee('khachhang@example.com')
            ->assertDontSee('nguoidung@example.com');
    }

    public function test_admin_can_view_customer_detail(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Tran Van Binh',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.customers.show', $customer))
            ->assertOk()
            ->assertSee('Tran Van Binh')
            ->assertSee('Thông tin cá nhân');
    }

    public function test_admin_cannot_view_non_customer_as_customer(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.customers.show', $admin))
            ->assertNotFound();
    }

    public function test_customer_cannot_view_another_customer_detail(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $otherCustomer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($customer)
            ->get(route('admin.customers.show', $otherCustomer))
            ->assertForbidden();
    }


    public function test_customer_list_does_not_display_other_roles(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Administrator Hidden',
        ]);

        User::factory()->create([
            'role' => 'staff',
            'name' => 'Staff Hidden',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Customer Visible',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.customers.index'));

        $response->assertOk()
            ->assertSee('Customer Visible');

        // Kiểm tra trực tiếp danh sách được Controller truyền vào View.
        $customers = $response->viewData('customers');

        $this->assertEquals(1, $customers->total());

        $this->assertEquals(
            [$customer->id],
            $customers->getCollection()->pluck('id')->all()
        );

        $this->assertTrue(
            $customers->getCollection()->every(
                fn($user) => $user->role === 'customer'
            )
        );
    }

    public function test_admin_can_search_customer_by_phone(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        User::factory()->create([
            'role' => 'customer',
            'name' => 'Khach Hang A',
            'phone' => '0901234567',
        ]);

        User::factory()->create([
            'role' => 'customer',
            'name' => 'Khach Hang B',
            'phone' => '0987654321',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.customers.index', [
                'search' => '0901234567',
            ]))
            ->assertOk()
            ->assertSee('Khach Hang A')
            ->assertDontSee('Khach Hang B');
    }

    public function test_customer_list_is_paginated(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        User::factory()->count(15)->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.customers.index'));

        $response->assertOk();

        $this->assertCount(
            10,
            $response->viewData('customers')->items()
        );

        $this->assertEquals(
            15,
            $response->viewData('customers')->total()
        );
    }

    public function test_customer_detail_has_empty_booking_history_when_no_bookings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.customers.show', $customer))
            ->assertOk()
            ->assertSee('Chưa có đơn đặt vé liên kết với tài khoản này.');
    }

    public function test_customer_detail_does_not_include_another_customers_booking(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.customers.show', $customer));

        $response->assertOk();

        $this->assertEquals(
            0,
            $response->viewData('totalBookings')
        );
    }


    public function test_customer_history_only_contains_own_bookings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $customerA = User::factory()->create([
            'role' => 'customer',
            'name' => 'Customer A',
        ]);

        $customerB = User::factory()->create([
            'role' => 'customer',
            'name' => 'Customer B',
        ]);

        $flight = $this->createTestFlight();

        $bookingA = $this->createTestBooking(
            $flight,
            $customerA,
            'ABC123',
            'paid',
            1500000
        );

        $this->createTestBooking(
            $flight,
            $customerB,
            'XYZ789',
            'paid',
            2000000
        );

        $response = $this->actingAs($admin)
            ->get(route('admin.customers.show', $customerA));

        $response->assertOk()
            ->assertSee('ABC123')
            ->assertDontSee('XYZ789');

        $bookings = $response->viewData('bookings');

        $this->assertEquals(1, $bookings->total());
        $this->assertEquals(
            $bookingA->id,
            $bookings->first()->id
        );
    }


    public function test_customer_total_paid_only_counts_paid_bookings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $flight = $this->createTestFlight();

        $this->createTestBooking(
            $flight,
            $customer,
            'PAID01',
            'paid',
            1500000
        );

        $this->createTestBooking(
            $flight,
            $customer,
            'WAIT01',
            'pending',
            2000000
        );

        $this->createTestBooking(
            $flight,
            $customer,
            'APP001',
            'pending_approval',
            3000000
        );

        $response = $this->actingAs($admin)
            ->get(route('admin.customers.show', $customer));

        $response->assertOk();

        $this->assertEquals(
            3,
            $response->viewData('totalBookings')
        );

        $this->assertEquals(
            1500000,
            $response->viewData('totalPaid')
        );
    }


    public function test_guest_booking_with_same_email_is_not_linked_to_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'khach@example.com',
        ]);

        $flight = $this->createTestFlight();

        $guestBooking = $this->createTestBooking(
            $flight,
            null,
            'GUEST1',
            'paid',
            1500000
        );

        $guestBooking->update([
            'passenger_email' => 'khach@example.com',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.customers.show', $customer));

        $response->assertOk()
            ->assertDontSee('GUEST1');

        $this->assertEquals(
            0,
            $response->viewData('totalBookings')
        );

        $this->assertEquals(
            0,
            $response->viewData('totalPaid')
        );
    }


    private function createTestFlight(): Flight
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
            'available_seats' => 20,
            'status' => 'scheduled',
        ]);
    }

    private function createTestBooking(
        Flight $flight,
        ?User $customer,
        string $code,
        string $status,
        int $price
    ): Booking {
        return Booking::create([
            'user_id' => $customer?->id,
            'pnr_code' => $code,
            'booking_code' => $code,
            'flight_id' => $flight->id,
            'passenger_name' => $customer?->name ?? 'Khach Vang Lai',
            'passenger_email' => $customer?->email ?? 'guest@example.com',
            'passenger_phone' => '0912345678',
            'passenger_count' => 1,
            'number_of_seats' => 1,
            'total_price' => $price,
            'status' => $status,
        ]);
    }
}
