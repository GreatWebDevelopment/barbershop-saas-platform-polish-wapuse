<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Customer;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Shop;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EnterpriseSeeder extends Seeder
{
    public function run(): void
    {
        // Create company
        $company = Company::create([
            'name' => 'ClipMaster Franchise',
            'slug' => 'clipmaster',
            'email' => 'admin@clipmaster.com',
            'phone' => '(555) 000-0000',
            'description' => 'Premium barbershop franchise with locations across Texas',
            'branding' => ['primary_color' => '#D4A853', 'secondary_color' => '#1a1a2e'],
        ]);

        // 5 locations
        $locations = [
            ['name' => 'ClipMaster Downtown', 'address' => '123 Main St', 'city' => 'Austin', 'state' => 'TX', 'zip' => '78701', 'latitude' => 30.2672, 'longitude' => -97.7431, 'phone' => '(512) 555-0001'],
            ['name' => 'ClipMaster Southside', 'address' => '456 S Congress Ave', 'city' => 'Austin', 'state' => 'TX', 'zip' => '78704', 'latitude' => 30.2460, 'longitude' => -97.7489, 'phone' => '(512) 555-0002'],
            ['name' => 'ClipMaster North', 'address' => '789 N Lamar Blvd', 'city' => 'Austin', 'state' => 'TX', 'zip' => '78756', 'latitude' => 30.3160, 'longitude' => -97.7530, 'phone' => '(512) 555-0003'],
            ['name' => 'ClipMaster Round Rock', 'address' => '321 E Main St', 'city' => 'Round Rock', 'state' => 'TX', 'zip' => '78664', 'latitude' => 30.5083, 'longitude' => -97.6789, 'phone' => '(512) 555-0004'],
            ['name' => 'ClipMaster San Marcos', 'address' => '654 Guadalupe St', 'city' => 'San Marcos', 'state' => 'TX', 'zip' => '78666', 'latitude' => 29.8833, 'longitude' => -97.9414, 'phone' => '(512) 555-0005'],
        ];

        $hours = [
            'monday' => '9:00 AM - 8:00 PM',
            'tuesday' => '9:00 AM - 8:00 PM',
            'wednesday' => '9:00 AM - 8:00 PM',
            'thursday' => '9:00 AM - 8:00 PM',
            'friday' => '9:00 AM - 9:00 PM',
            'saturday' => '8:00 AM - 6:00 PM',
            'sunday' => '10:00 AM - 5:00 PM',
        ];

        $staffNames = [
            ['Marcus Johnson', 'Senior Stylist'], ['Tanya Williams', 'Master Barber'],
            ['Derek Chen', 'Stylist'], ['Maria Garcia', 'Stylist'],
            ['James Wilson', 'Junior Stylist'], ['Aisha Patel', 'Senior Barber'],
            ['Brandon Lee', 'Stylist'], ['Crystal Moore', 'Master Stylist'],
            ['Tyler Brown', 'Barber'], ['Nicole Davis', 'Stylist'],
            ['Andre Thomas', 'Senior Barber'], ['Sophia Martinez', 'Stylist'],
            ['Ryan Jackson', 'Junior Barber'], ['Jasmine White', 'Stylist'],
            ['David Kim', 'Master Barber'],
        ];

        $services = [
            ['name' => 'Classic Haircut', 'price' => 25.00, 'duration_minutes' => 20],
            ['name' => 'Premium Cut & Style', 'price' => 35.00, 'duration_minutes' => 30],
            ['name' => 'Buzz Cut', 'price' => 18.00, 'duration_minutes' => 15],
            ['name' => 'Beard Trim', 'price' => 15.00, 'duration_minutes' => 15],
            ['name' => 'Hot Towel Shave', 'price' => 30.00, 'duration_minutes' => 25],
            ['name' => 'Kids Cut (12 & under)', 'price' => 18.00, 'duration_minutes' => 15],
            ['name' => 'Fade', 'price' => 30.00, 'duration_minutes' => 25],
            ['name' => 'Hair & Beard Combo', 'price' => 45.00, 'duration_minutes' => 40],
        ];

        $staffIdx = 0;
        foreach ($locations as $locData) {
            $shop = Shop::create(array_merge($locData, [
                'company_id' => $company->id,
                'hours' => $hours,
                'queue_capacity' => 30,
                'queue_enabled' => true,
                'amenities' => ['wifi', 'tv', 'drinks', 'magazines'],
                'timezone' => 'America/Chicago',
                'display_passcode' => '1234',
            ]));

            // Create admin user for this shop
            User::create([
                'name' => "Admin - {$shop->name}",
                'email' => "admin-{$shop->id}@clipmaster.com",
                'password' => Hash::make('password'),
                'shop_id' => $shop->id,
                'role' => 'admin',
            ]);

            // Create service category and services
            $category = ServiceCategory::create([
                'shop_id' => $shop->id,
                'name' => 'Haircuts & Grooming',
            ]);

            foreach ($services as $svc) {
                Service::create(array_merge($svc, [
                    'shop_id' => $shop->id,
                    'service_category_id' => $category->id,
                ]));
            }

            // 3 staff per location
            for ($i = 0; $i < 3; $i++) {
                $s = $staffNames[$staffIdx % count($staffNames)];
                Staff::create([
                    'shop_id' => $shop->id,
                    'name' => $s[0],
                    'title' => $s[1],
                    'email' => strtolower(str_replace(' ', '.', $s[0])) . "@clipmaster.com",
                    'phone' => '(512) 555-' . str_pad($staffIdx + 10, 4, '0', STR_PAD_LEFT),
                    'years_experience' => rand(1, 15),
                    'hourly_rate' => rand(15, 35),
                    'commission_percent' => rand(30, 60),
                    'specialties' => ['fade', 'beard trim', 'hot towel shave'],
                    'status' => 'active',
                    'queue_status' => 'active',
                ]);
                $staffIdx++;
            }

            // Create some customers
            $customerNames = [
                ['John', 'Smith'], ['Mike', 'Johnson'], ['Chris', 'Brown'],
                ['David', 'Williams'], ['Tom', 'Davis'],
            ];

            foreach ($customerNames as $cn) {
                Customer::create([
                    'shop_id' => $shop->id,
                    'first_name' => $cn[0],
                    'last_name' => $cn[1],
                    'email' => strtolower("{$cn[0]}.{$cn[1]}") . rand(1, 99) . "@example.com",
                    'phone' => '(555) ' . rand(100, 999) . '-' . rand(1000, 9999),
                ]);
            }

            // Create some queue entries for first 3 shops
            if ($shop->id <= 3) {
                $shopStaff = Staff::where('shop_id', $shop->id)->first();
                $shopServices = Service::where('shop_id', $shop->id)->get();

                for ($q = 1; $q <= rand(3, 8); $q++) {
                    QueueEntry::create([
                        'shop_id' => $shop->id,
                        'queue_number' => strtoupper(substr(dechex($shop->id), 0, 1)) . str_pad($q, 3, '0', STR_PAD_LEFT),
                        'customer_name' => fake()->name(),
                        'customer_phone' => fake()->phoneNumber(),
                        'party_size' => rand(1, 3),
                        'service_id' => $shopServices->random()->id,
                        'status' => $q <= 1 ? 'in_service' : ($q <= 2 ? 'called' : 'waiting'),
                        'staff_id' => $q <= 2 ? $shopStaff->id : null,
                        'position' => $q <= 2 ? 0 : $q - 2,
                        'estimated_wait_minutes' => max(0, ($q - 2) * 15),
                        'checked_in_at' => now()->subMinutes(rand(5, 60)),
                        'called_at' => $q <= 2 ? now()->subMinutes(rand(1, 10)) : null,
                        'service_started_at' => $q <= 1 ? now()->subMinutes(rand(1, 5)) : null,
                    ]);
                }
            }
        }
    }
}
