<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Shop;
use App\Models\Staff;
use App\Notifications\AppointmentBooked;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index(Shop $shop)
    {
        $shop->load([
            'serviceCategories' => fn ($q) => $q->orderBy('sort_order'),
            'services' => fn ($q) => $q->where('status', 'active')->orderBy('sort_order'),
            'services.addOns',
            'services.category',
            'staff' => fn ($q) => $q->where('status', 'active'),
        ]);

        return Inertia::render('Booking/Index', [
            'shop' => $shop,
        ]);
    }

    public function availability(Request $request, Shop $shop): JsonResponse
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date|after_or_equal:today',
            'service_id' => 'required|exists:services,id',
        ]);

        $staff = Staff::withoutGlobalScopes()->where('shop_id', $shop->id)->findOrFail($request->staff_id);
        $service = $shop->services()->findOrFail($request->service_id);
        $date = Carbon::parse($request->date);
        $duration = $service->duration_minutes + ($request->integer('addon_duration', 0));

        $slots = $this->calculateSlots($staff, $date, $duration);

        return response()->json(['slots' => $slots]);
    }

    public function store(Request $request, Shop $shop): JsonResponse
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'addon_ids' => 'array',
            'addon_ids.*' => 'exists:service_add_ons,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'recurrence_type' => 'in:none,weekly,biweekly,monthly',
            'recurrence_end_date' => 'nullable|date|after:date',
        ]);

        $service = $shop->services()->findOrFail($validated['service_id']);
        $staff = Staff::withoutGlobalScopes()->where('shop_id', $shop->id)->findOrFail($validated['staff_id']);

        // Calculate total price and duration including add-ons
        $totalPrice = $service->price;
        $totalDuration = $service->duration_minutes;
        if (!empty($validated['addon_ids'])) {
            $addons = $service->addOns()->whereIn('service_add_ons.id', $validated['addon_ids'])->get();
            $totalPrice += $addons->sum('price');
            $totalDuration += $addons->sum('duration_minutes');
        }

        $startsAt = Carbon::parse($validated['date'] . ' ' . $validated['time']);

        // Verify availability
        if (!$staff->isAvailableAt($startsAt, $totalDuration)) {
            return response()->json(['message' => 'This time slot is no longer available.'], 422);
        }

        // Find or create customer
        $customer = Customer::withoutGlobalScopes()
            ->where('shop_id', $shop->id)
            ->where('email', $validated['customer_email'])
            ->first();

        if (!$customer) {
            $nameParts = explode(' ', $validated['customer_name'], 2);
            $customer = Customer::withoutGlobalScopes()->create([
                'shop_id' => $shop->id,
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'email' => $validated['customer_email'],
                'phone' => $validated['customer_phone'],
            ]);
        }

        $recurrenceType = $validated['recurrence_type'] ?? 'none';
        $recurrenceEnd = $validated['recurrence_end_date'] ?? null;

        // Create appointment(s)
        $appointments = [];
        $dates = $this->getRecurrenceDates($startsAt, $recurrenceType, $recurrenceEnd);

        foreach ($dates as $appointmentDate) {
            $start = $appointmentDate->copy()->setTimeFrom($startsAt);
            $appointments[] = Appointment::withoutGlobalScopes()->create([
                'shop_id' => $shop->id,
                'customer_id' => $customer->id,
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'starts_at' => $start,
                'ends_at' => $start->copy()->addMinutes($totalDuration),
                'status' => 'scheduled',
                'price' => $totalPrice,
                'is_walkin' => false,
                'booked_online' => true,
                'recurrence_type' => $recurrenceType,
                'recurrence_end_date' => $recurrenceEnd,
            ]);
        }

        // Send notification
        $customer->notify(new AppointmentBooked($appointments[0], $shop, $staff, $service));

        return response()->json([
            'message' => 'Appointment booked successfully!',
            'appointment' => $appointments[0]->load(['staff', 'service', 'customer']),
        ]);
    }

    private function calculateSlots(Staff $staff, Carbon $date, int $duration): array
    {
        $schedule = $staff->availability_schedule;
        if (!$schedule) return [];

        $dayName = strtolower($date->format('l'));
        $daySchedule = $schedule[$dayName] ?? null;

        if (!$daySchedule || empty($daySchedule['enabled'])) return [];

        $blocks = $daySchedule['blocks'] ?? [];
        $breaks = $daySchedule['breaks'] ?? [];
        $slots = [];

        foreach ($blocks as $block) {
            $blockStart = $date->copy()->setTimeFromTimeString($block['start']);
            $blockEnd = $date->copy()->setTimeFromTimeString($block['end']);

            $current = $blockStart->copy();
            while ($current->copy()->addMinutes($duration)->lte($blockEnd)) {
                $slotEnd = $current->copy()->addMinutes($duration);

                // Check breaks
                $inBreak = false;
                foreach ($breaks as $brk) {
                    $breakStart = $date->copy()->setTimeFromTimeString($brk['start']);
                    $breakEnd = $date->copy()->setTimeFromTimeString($brk['end']);
                    if ($current->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                        $inBreak = true;
                        break;
                    }
                }

                if (!$inBreak && $staff->isAvailableAt($current, $duration)) {
                    // Don't show past slots for today
                    if (!$date->isToday() || $current->gt(now())) {
                        $slots[] = $current->format('H:i');
                    }
                }

                $current->addMinutes(15);
            }
        }

        return $slots;
    }

    private function getRecurrenceDates(Carbon $start, string $type, ?string $endDate): array
    {
        if ($type === 'none' || !$endDate) {
            return [$start];
        }

        $end = Carbon::parse($endDate);
        $dates = [$start];
        $current = $start->copy();

        while (true) {
            $current = match ($type) {
                'weekly' => $current->copy()->addWeek(),
                'biweekly' => $current->copy()->addWeeks(2),
                'monthly' => $current->copy()->addMonth(),
                default => null,
            };

            if (!$current || $current->gt($end)) break;
            $dates[] = $current;
        }

        return $dates;
    }
}
