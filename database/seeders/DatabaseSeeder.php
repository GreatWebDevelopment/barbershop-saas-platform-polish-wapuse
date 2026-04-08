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
        // === Demo Shop ===
        $shop = Shop::create([
            'name' => 'Classic Cuts & Co.',
            'address' => '123 Main Street',
            'city' => 'Austin',
            'state' => 'TX',
            'zip' => '78701',
            'phone' => '(512) 555-0123',
            'description' => 'Premium barbershop experience since 2010.',
            'hours' => [
                'monday' => '9:00 AM - 7:00 PM',
                'tuesday' => '9:00 AM - 7:00 PM',
                'wednesday' => '9:00 AM - 7:00 PM',
                'thursday' => '9:00 AM - 7:00 PM',
                'friday' => '9:00 AM - 7:00 PM',
                'saturday' => '8:00 AM - 5:00 PM',
                'sunday' => 'Closed',
            ],
        ]);

        // === Owner Account ===
        User::factory()->create([
            'name' => 'Demo Owner',
            'email' => 'demo@barberpro.com',
            'password' => bcrypt('password'),
            'shop_id' => $shop->id,
            'role' => 'owner',
        ]);

        // === Staff ===
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
        $staffNames = ['Mike Rodriguez', 'Sarah Johnson', 'Tony Kim'];

        // === Service Categories & Services (25+) ===
        $classicCuts = ServiceCategory::create(['shop_id' => $shop->id, 'name' => 'Classic Cuts', 'description' => 'Traditional barbershop cuts', 'sort_order' => 1]);
        $fadeCuts = ServiceCategory::create(['shop_id' => $shop->id, 'name' => 'Fade Cuts', 'description' => 'Modern fade styles', 'sort_order' => 2]);
        $specialty = ServiceCategory::create(['shop_id' => $shop->id, 'name' => 'Specialty Services', 'description' => 'Premium grooming services', 'sort_order' => 3]);
        $addOns = ServiceCategory::create(['shop_id' => $shop->id, 'name' => 'Add-Ons', 'description' => 'Extras to enhance your visit', 'sort_order' => 4]);
        $kidsCategory = ServiceCategory::create(['shop_id' => $shop->id, 'name' => 'Kids & Seniors', 'description' => 'Special pricing for kids and seniors', 'sort_order' => 5]);

        $services = [];

        foreach ([
            // Classic Cuts (4)
            [$classicCuts, 'Buzz Cut', 'Clean all-over clipper cut', 15.00, 15],
            [$classicCuts, 'Crew Cut', 'Classic tapered crew cut', 20.00, 20],
            [$classicCuts, 'Side Part', 'Traditional side part with comb finish', 25.00, 25],
            [$classicCuts, 'Pompadour', 'Vintage-inspired pompadour style', 30.00, 35],
            // Fade Cuts (4)
            [$fadeCuts, 'Low Fade', 'Subtle low fade with blending', 25.00, 30],
            [$fadeCuts, 'Mid Fade', 'Clean mid fade with taper', 28.00, 30],
            [$fadeCuts, 'High Fade', 'High and tight fade', 30.00, 35],
            [$fadeCuts, 'Skin Fade', 'Precision skin fade to the scalp', 35.00, 40],
            // Specialty Services (7)
            [$specialty, 'Beard Trim', 'Professional beard shaping and trim', 15.00, 15],
            [$specialty, 'Hot Towel Shave', 'Traditional hot towel straight razor shave', 40.00, 45],
            [$specialty, 'Eyebrow Trim', 'Clean eyebrow shaping', 10.00, 10],
            [$specialty, 'Hair Wash', 'Shampoo and conditioning treatment', 8.00, 10],
            [$specialty, 'Beard Sculpting', 'Detailed beard design and sculpting', 25.00, 25],
            [$specialty, 'Hair Color', 'Full color or highlights', 65.00, 60],
            [$specialty, 'Straight Razor Shave', 'Classic straight razor shave', 35.00, 35],
            // Add-Ons (6)
            [$addOns, 'Beard Trim Add-On', 'Add a beard trim to any cut', 10.00, 10],
            [$addOns, 'Hot Towel Add-On', 'Add a hot towel treatment', 8.00, 10],
            [$addOns, 'Eyebrow Trim Add-On', 'Add eyebrow shaping', 5.00, 5],
            [$addOns, 'Beard Oil Treatment', 'Premium beard conditioning', 8.00, 5],
            [$addOns, 'Hair Design', 'Custom line or design work', 15.00, 15],
            [$addOns, 'Scalp Massage', 'Relaxing scalp massage', 12.00, 10],
            // Kids & Seniors (4)
            [$kidsCategory, 'Kids Cut (Under 12)', 'Haircut for children under 12', 15.00, 20],
            [$kidsCategory, 'Teen Cut', 'Style cut for ages 12-17', 20.00, 25],
            [$kidsCategory, 'Senior Cut', 'Discounted cut for 65+', 18.00, 20],
            [$kidsCategory, 'Kids Buzz', 'Simple clipper cut for kids', 10.00, 10],
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

        // Main services only (exclude add-ons for appointments)
        $mainServices = array_slice($services, 0, 15);

        // === Customers (50) ===
        $usualServices = ['Buzz Cut', 'Crew Cut', 'Side Part', 'Low Fade', 'Mid Fade', 'Skin Fade', 'Beard Trim', 'Hot Towel Shave'];

        $customers = collect();
        for ($i = 0; $i < 50; $i++) {
            $customers->push(Customer::create([
                'shop_id' => $shop->id,
                'first_name' => fake()->firstName('male'),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->numerify('(512) 555-####'),
                'preferred_stylist' => $staffNames[array_rand($staffNames)],
                'notes' => fake()->optional(0.4)->randomElement([
                    'Prefers short on sides, longer on top',
                    'Sensitive scalp — use gentle products',
                    'Always gets a hot towel after',
                    'Likes to talk sports',
                    'Bring tablet for kids waiting',
                    'Allergic to certain hair products',
                    'Regular walk-in, no appointments usually',
                    'Prefers early morning slots',
                    'Tips well, very loyal customer',
                    'New customer, referred by friend',
                ]),
                'loyalty_points' => fake()->numberBetween(0, 500),
                'visit_count' => fake()->numberBetween(1, 40),
                'last_visit' => fake()->dateTimeBetween('-6 months', 'now'),
            ]));
        }

        // === Appointments (220+) ===
        // Target distribution: completed 60%, confirmed 15%, pending 10%, cancelled 10%, no_show 5%
        $paymentMethods = ['cash', 'card', 'apple_pay', null];
        $now = Carbon::now();

        for ($i = 0; $i < 230; $i++) {
            $isPast = $i < 200;
            $daysOffset = $isPast ? rand(1, 180) : rand(-14, -1);
            $date = $now->copy()->subDays($daysOffset);

            // Skip Sundays
            if ($date->dayOfWeek === Carbon::SUNDAY) {
                $date->addDay();
            }

            $isSaturday = $date->dayOfWeek === Carbon::SATURDAY;
            $minHour = $isSaturday ? 8 : 9;
            $maxHour = $isSaturday ? 16 : 18;
            $hour = rand($minHour, $maxHour);
            $startsAt = $date->setHour($hour)->setMinute(rand(0, 3) * 15)->setSecond(0);

            $service = $mainServices[array_rand($mainServices)];

            // Weight staff: Mike gets ~45%, Sarah ~35%, Tony ~20%
            $roll = rand(1, 100);
            $staff = $roll <= 45 ? $mike : ($roll <= 80 ? $sarah : $tony);

            if ($isPast) {
                // 60% completed, 15% confirmed, 10% pending, 10% cancelled, 5% no_show
                $statusRoll = rand(1, 100);
                if ($statusRoll <= 60) {
                    $status = 'completed';
                } elseif ($statusRoll <= 75) {
                    $status = 'confirmed';
                } elseif ($statusRoll <= 85) {
                    $status = 'pending';
                } elseif ($statusRoll <= 95) {
                    $status = 'cancelled';
                } else {
                    $status = 'no-show';
                }
            } else {
                $status = rand(0, 1) ? 'confirmed' : 'pending';
            }

            $paymentStatus = 'pending';
            $paymentMethod = null;
            if ($status === 'completed') {
                $paymentStatus = 'paid';
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)] ?? 'cash';
            } elseif ($status === 'cancelled') {
                $paymentStatus = 'refunded';
            }

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
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
            ]);
        }

        // Run enterprise multi-location seeder
        $this->call(EnterpriseSeeder::class);
    }
}
