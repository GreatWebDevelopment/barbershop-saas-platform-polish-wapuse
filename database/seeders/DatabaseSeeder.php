<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Shop;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $shop = Shop::create([
            'name' => 'Classic Cuts & Co.',
            'address' => '742 Barber Lane',
            'city' => 'Austin',
            'state' => 'TX',
            'zip' => '78701',
            'phone' => '(512) 555-0187',
            'description' => 'Premium barbershop experience since 2010.',
            'hours' => [
                'monday' => '9:00 AM - 7:00 PM',
                'tuesday' => '9:00 AM - 7:00 PM',
                'wednesday' => '9:00 AM - 7:00 PM',
                'thursday' => '9:00 AM - 8:00 PM',
                'friday' => '9:00 AM - 8:00 PM',
                'saturday' => '8:00 AM - 6:00 PM',
                'sunday' => 'Closed',
            ],
        ]);

        User::factory()->create([
            'name' => 'Demo Admin',
            'email' => 'demo@classiccutsco.com',
            'password' => bcrypt('password'),
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);

        $mike = Staff::create([
            'shop_id' => $shop->id,
            'name' => 'Mike Rodriguez',
            'title' => 'Master Barber',
            'email' => 'mike@classiccutsco.com',
            'phone' => '(512) 555-0101',
            'years_experience' => 15,
            'hourly_rate' => 35.00,
            'commission_percent' => 60,
            'specialties' => ['Fades', 'Classic cuts', 'Straight razor shaves'],
            'status' => 'active',
            'bio' => 'Veteran barber with 15 years of experience specializing in precision fades and classic gentleman cuts.',
        ]);

        $sarah = Staff::create([
            'shop_id' => $shop->id,
            'name' => 'Sarah Johnson',
            'title' => 'Style Specialist',
            'email' => 'sarah@classiccutsco.com',
            'phone' => '(512) 555-0102',
            'years_experience' => 8,
            'hourly_rate' => 30.00,
            'commission_percent' => 55,
            'specialties' => ['Modern cuts', 'Color', 'Beard styling'],
            'status' => 'active',
            'bio' => 'Creative stylist blending classic barbering with modern trends and color techniques.',
        ]);

        $tony = Staff::create([
            'shop_id' => $shop->id,
            'name' => 'Tony Kim',
            'title' => 'Junior Barber',
            'email' => 'tony@classiccutsco.com',
            'phone' => '(512) 555-0103',
            'years_experience' => 2,
            'hourly_rate' => 20.00,
            'commission_percent' => 45,
            'specialties' => ['Basic cuts', 'Kids cuts'],
            'status' => 'active',
            'bio' => 'Eager young barber building his skills with great energy and attention to detail.',
        ]);

        $staffMembers = [$mike, $sarah, $tony];

        $classicCuts = ServiceCategory::create(['shop_id' => $shop->id, 'name' => 'Classic Cuts', 'description' => 'Traditional barbershop cuts', 'sort_order' => 1]);
        $fadeCuts = ServiceCategory::create(['shop_id' => $shop->id, 'name' => 'Fade Cuts', 'description' => 'Modern fade styles', 'sort_order' => 2]);
        $specialty = ServiceCategory::create(['shop_id' => $shop->id, 'name' => 'Specialty Services', 'description' => 'Premium grooming services', 'sort_order' => 3]);
        $addOns = ServiceCategory::create(['shop_id' => $shop->id, 'name' => 'Add-Ons', 'description' => 'Extras to enhance your visit', 'sort_order' => 4]);

        $services = [];

        foreach ([
            [$classicCuts, "Gentleman's Cut", 'Classic scissor-over-comb haircut', 30.00, 30],
            [$classicCuts, 'Buzz Cut', 'Clean all-over clipper cut', 20.00, 15],
            [$classicCuts, 'Kids Cut', 'Haircut for children 12 and under', 18.00, 20],
            [$fadeCuts, 'Skin Fade', 'Precision skin fade with blending', 35.00, 35],
            [$fadeCuts, 'Mid Fade', 'Clean mid fade with taper', 32.00, 30],
            [$fadeCuts, 'Drop Fade', 'Dropped fade line behind the ear', 35.00, 35],
            [$specialty, 'Straight Razor Shave', 'Hot towel straight razor shave', 40.00, 40],
            [$specialty, 'Beard Sculpting', 'Precision beard shaping and trim', 25.00, 25],
            [$specialty, 'Hair Color', 'Full color or highlights', 65.00, 60],
            [$addOns, 'Hot Towel Treatment', 'Relaxing hot towel facial', 10.00, 10],
            [$addOns, 'Beard Oil Treatment', 'Premium beard conditioning', 8.00, 5],
            [$addOns, 'Hair Design', 'Custom line or design work', 15.00, 15],
        ] as [$cat, $name, $desc, $price, $dur]) {
            $services[] = Service::create([
                'shop_id' => $shop->id,
                'service_category_id' => $cat->id,
                'name' => $name,
                'description' => $desc,
                'price' => $price,
                'duration_minutes' => $dur,
            ]);
        }

        $customers = Customer::factory(50)->create([
            'shop_id' => $shop->id,
        ]);

        $statuses = ['completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'cancelled', 'no-show', 'pending', 'confirmed'];
        $paymentMethods = ['cash', 'cash', 'card', 'card', 'card', 'paypal'];
        $paymentStatuses = ['paid', 'paid', 'paid', 'pending', 'refunded'];

        for ($i = 0; $i < 220; $i++) {
            $daysAgo = $i < 200 ? rand(1, 180) : rand(-14, -1);
            $hour = rand(9, 17);
            $startsAt = Carbon::now()->addDays(-$daysAgo)->setHour($hour)->setMinute(rand(0, 1) * 30)->setSecond(0);
            $service = $services[array_rand($services)];
            $staff = $staffMembers[array_rand($staffMembers)];

            $status = $daysAgo > 0 ? $statuses[array_rand($statuses)] : (rand(0, 1) ? 'confirmed' : 'pending');

            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $paymentStatus = $status === 'completed' ? $paymentStatuses[array_rand($paymentStatuses)] : ($status === 'cancelled' ? 'refunded' : 'pending');

            Appointment::create([
                'shop_id' => $shop->id,
                'customer_id' => $customers->random()->id,
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'starts_at' => $startsAt,
                'ends_at' => $startsAt->copy()->addMinutes($service->duration_minutes),
                'status' => $status,
                'price' => $service->price,
                'is_walkin' => rand(0, 4) === 0,
                'notes' => rand(0, 5) === 0 ? fake()->sentence() : null,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
            ]);
        }
    }
}
