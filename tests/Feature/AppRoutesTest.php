<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Shop;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppRoutesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Shop $shop;
    private Staff $staffMember;
    private Service $service;
    private ServiceCategory $category;
    private Customer $customer;
    private Appointment $appointment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shop = Shop::create([
            'name' => 'Test Shop',
            'address' => '123 Main St',
            'city' => 'Austin',
            'state' => 'TX',
            'zip' => '78701',
            'phone' => '555-0100',
            'hours' => ['monday' => '9-5'],
        ]);

        $this->user = User::factory()->create([
            'shop_id' => $this->shop->id,
            'role' => 'admin',
        ]);

        $this->staffMember = Staff::create([
            'shop_id' => $this->shop->id,
            'name' => 'Test Barber',
            'title' => 'Barber',
            'hourly_rate' => 25,
            'commission_percent' => 50,
            'status' => 'active',
        ]);

        $this->category = ServiceCategory::create([
            'shop_id' => $this->shop->id,
            'name' => 'Cuts',
            'sort_order' => 1,
        ]);

        $this->service = Service::create([
            'shop_id' => $this->shop->id,
            'service_category_id' => $this->category->id,
            'name' => 'Haircut',
            'price' => 30,
            'duration_minutes' => 30,
        ]);

        $this->customer = Customer::create([
            'shop_id' => $this->shop->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '555-0101',
        ]);

        $this->appointment = Appointment::create([
            'shop_id' => $this->shop->id,
            'customer_id' => $this->customer->id,
            'staff_id' => $this->staffMember->id,
            'service_id' => $this->service->id,
            'starts_at' => now()->addHour(),
            'ends_at' => now()->addHours(2),
            'status' => 'confirmed',
            'price' => 30,
        ]);
    }

    // Public routes
    public function test_landing_page_loads(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_register_page_loads(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    // Auth redirects for unauthenticated users
    public function test_dashboard_redirects_unauthenticated(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_appointments_redirects_unauthenticated(): void
    {
        $this->get('/appointments')->assertRedirect('/login');
    }

    // Dashboard
    public function test_dashboard_loads(): void
    {
        $this->actingAs($this->user)->get('/dashboard')->assertStatus(200);
    }

    // Appointments CRUD
    public function test_appointments_index_loads(): void
    {
        $this->actingAs($this->user)->get('/appointments')->assertStatus(200);
    }

    public function test_appointments_index_with_search(): void
    {
        $this->actingAs($this->user)->get('/appointments?search=John')->assertStatus(200);
    }

    public function test_appointments_index_with_status_filter(): void
    {
        $this->actingAs($this->user)->get('/appointments?status=confirmed')->assertStatus(200);
    }

    public function test_appointments_create_loads(): void
    {
        $this->actingAs($this->user)->get('/appointments/create')->assertStatus(200);
    }

    public function test_appointments_store(): void
    {
        $this->actingAs($this->user)->post('/appointments', [
            'customer_id' => $this->customer->id,
            'staff_id' => $this->staffMember->id,
            'service_id' => $this->service->id,
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'status' => 'pending',
            'is_walkin' => false,
            'notes' => '',
        ])->assertRedirect('/appointments');

        $this->assertDatabaseCount('appointments', 2);
    }

    public function test_appointments_store_validation(): void
    {
        $this->actingAs($this->user)->post('/appointments', [])
            ->assertSessionHasErrors(['customer_id', 'staff_id', 'service_id', 'starts_at', 'status']);
    }

    public function test_appointments_edit_loads(): void
    {
        $this->actingAs($this->user)
            ->get("/appointments/{$this->appointment->id}/edit")
            ->assertStatus(200);
    }

    public function test_appointments_update(): void
    {
        $this->actingAs($this->user)->put("/appointments/{$this->appointment->id}", [
            'customer_id' => $this->customer->id,
            'staff_id' => $this->staffMember->id,
            'service_id' => $this->service->id,
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'status' => 'completed',
            'is_walkin' => false,
            'notes' => 'Updated',
        ])->assertRedirect('/appointments');
    }

    public function test_appointments_delete(): void
    {
        $this->actingAs($this->user)
            ->delete("/appointments/{$this->appointment->id}")
            ->assertRedirect('/appointments');

        $this->assertDatabaseCount('appointments', 0);
    }

    // Staff CRUD
    public function test_staff_index_loads(): void
    {
        $this->actingAs($this->user)->get('/staff')->assertStatus(200);
    }

    public function test_staff_create_loads(): void
    {
        $this->actingAs($this->user)->get('/staff/create')->assertStatus(200);
    }

    public function test_staff_store(): void
    {
        $this->actingAs($this->user)->post('/staff', [
            'name' => 'New Barber',
            'title' => 'Junior',
            'email' => 'new@test.com',
            'phone' => '555-9999',
            'years_experience' => 1,
            'hourly_rate' => 20,
            'commission_percent' => 40,
            'specialties' => ['Cuts'],
            'bio' => 'New hire',
            'status' => 'active',
        ])->assertRedirect('/staff');

        $this->assertDatabaseHas('staff', ['name' => 'New Barber']);
    }

    public function test_staff_store_validation(): void
    {
        $this->actingAs($this->user)->post('/staff', [])
            ->assertSessionHasErrors(['name', 'status']);
    }

    public function test_staff_edit_loads(): void
    {
        $this->actingAs($this->user)
            ->get("/staff/{$this->staffMember->id}/edit")
            ->assertStatus(200);
    }

    public function test_staff_update(): void
    {
        $this->actingAs($this->user)->put("/staff/{$this->staffMember->id}", [
            'name' => 'Updated Barber',
            'title' => 'Senior',
            'years_experience' => 5,
            'hourly_rate' => 30,
            'commission_percent' => 55,
            'status' => 'active',
        ])->assertRedirect('/staff');
    }

    public function test_staff_delete(): void
    {
        // Create a staff member without appointments to delete cleanly
        $staff = Staff::create([
            'shop_id' => $this->shop->id,
            'name' => 'Deletable',
            'status' => 'active',
        ]);

        $this->actingAs($this->user)->delete("/staff/{$staff->id}")->assertRedirect('/staff');
    }

    // Services CRUD
    public function test_services_index_loads(): void
    {
        $this->actingAs($this->user)->get('/services')->assertStatus(200);
    }

    public function test_services_index_with_category_filter(): void
    {
        $this->actingAs($this->user)
            ->get("/services?category={$this->category->id}")
            ->assertStatus(200);
    }

    public function test_services_create_loads(): void
    {
        $this->actingAs($this->user)->get('/services/create')->assertStatus(200);
    }

    public function test_services_store(): void
    {
        $this->actingAs($this->user)->post('/services', [
            'name' => 'New Service',
            'description' => 'A new service',
            'service_category_id' => $this->category->id,
            'price' => 25,
            'duration_minutes' => 20,
            'status' => 'active',
        ])->assertRedirect('/services');

        $this->assertDatabaseHas('services', ['name' => 'New Service']);
    }

    public function test_services_store_validation(): void
    {
        $this->actingAs($this->user)->post('/services', [])
            ->assertSessionHasErrors(['name', 'service_category_id', 'price', 'duration_minutes', 'status']);
    }

    public function test_services_edit_loads(): void
    {
        $this->actingAs($this->user)
            ->get("/services/{$this->service->id}/edit")
            ->assertStatus(200);
    }

    public function test_services_update(): void
    {
        $this->actingAs($this->user)->put("/services/{$this->service->id}", [
            'name' => 'Updated Service',
            'description' => 'Updated',
            'service_category_id' => $this->category->id,
            'price' => 35,
            'duration_minutes' => 25,
            'status' => 'active',
        ])->assertRedirect('/services');
    }

    public function test_services_delete(): void
    {
        $svc = Service::create([
            'shop_id' => $this->shop->id,
            'service_category_id' => $this->category->id,
            'name' => 'Deletable',
            'price' => 10,
            'duration_minutes' => 10,
        ]);

        $this->actingAs($this->user)->delete("/services/{$svc->id}")->assertRedirect('/services');
    }

    // Customers CRUD
    public function test_customers_index_loads(): void
    {
        $this->actingAs($this->user)->get('/customers')->assertStatus(200);
    }

    public function test_customers_index_with_search(): void
    {
        $this->actingAs($this->user)->get('/customers?search=John')->assertStatus(200);
    }

    public function test_customers_create_loads(): void
    {
        $this->actingAs($this->user)->get('/customers/create')->assertStatus(200);
    }

    public function test_customers_store(): void
    {
        $this->actingAs($this->user)->post('/customers', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'phone' => '555-0202',
            'notes' => '',
        ])->assertRedirect('/customers');

        $this->assertDatabaseHas('customers', ['first_name' => 'Jane']);
    }

    public function test_customers_store_validation(): void
    {
        $this->actingAs($this->user)->post('/customers', [])
            ->assertSessionHasErrors(['first_name', 'last_name']);
    }

    public function test_customers_edit_loads(): void
    {
        $this->actingAs($this->user)
            ->get("/customers/{$this->customer->id}/edit")
            ->assertStatus(200);
    }

    public function test_customers_update(): void
    {
        $this->actingAs($this->user)->put("/customers/{$this->customer->id}", [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'updated@example.com',
        ])->assertRedirect('/customers');
    }

    public function test_customers_delete(): void
    {
        $cust = Customer::create([
            'shop_id' => $this->shop->id,
            'first_name' => 'Del',
            'last_name' => 'Ete',
        ]);

        $this->actingAs($this->user)->delete("/customers/{$cust->id}")->assertRedirect('/customers');
    }

    // Profile
    public function test_profile_page_loads(): void
    {
        $this->actingAs($this->user)->get('/profile')->assertStatus(200);
    }

    public function test_profile_update(): void
    {
        $this->actingAs($this->user)->patch('/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@test.com',
        ])->assertRedirect('/profile');
    }
}
