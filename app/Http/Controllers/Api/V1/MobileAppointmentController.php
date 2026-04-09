<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Shop;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileAppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Find customer records linked to this user's email across all shops
        $customerIds = Customer::withoutGlobalScopes()
            ->where('email', $user->email)
            ->pluck('id');

        $query = Appointment::withoutGlobalScopes()
            ->whereIn('customer_id', $customerIds)
            ->with(['staff:id,name,title,avatar_url', 'service:id,name,price,duration_minutes', 'shop:id,name,address,city,state'])
            ->orderBy('starts_at', 'desc');

        if ($request->has('upcoming')) {
            $query->where('starts_at', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->orderBy('starts_at', 'asc');
        }

        $appointments = $query->limit(50)->get();

        return response()->json(['appointments' => $appointments]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'addon_ids' => 'array',
            'addon_ids.*' => 'exists:service_add_ons,id',
        ]);

        $user = $request->user();
        $shop = Shop::findOrFail($validated['shop_id']);
        $service = $shop->services()->findOrFail($validated['service_id']);
        $staff = Staff::withoutGlobalScopes()->where('shop_id', $shop->id)->findOrFail($validated['staff_id']);

        $totalPrice = $service->price;
        $totalDuration = $service->duration_minutes;
        if (!empty($validated['addon_ids'])) {
            $addons = $service->addOns()->whereIn('service_add_ons.id', $validated['addon_ids'])->get();
            $totalPrice += $addons->sum('price');
            $totalDuration += $addons->sum('duration_minutes');
        }

        $startsAt = Carbon::parse($validated['date'] . ' ' . $validated['time']);

        if (!$staff->isAvailableAt($startsAt, $totalDuration)) {
            return response()->json(['message' => 'This time slot is no longer available.'], 422);
        }

        // Find or create customer for this shop
        $customer = Customer::withoutGlobalScopes()
            ->where('shop_id', $shop->id)
            ->where('email', $user->email)
            ->first();

        if (!$customer) {
            $nameParts = explode(' ', $user->name, 2);
            $customer = Customer::withoutGlobalScopes()->create([
                'shop_id' => $shop->id,
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'email' => $user->email,
                'phone' => $user->phone_number ?? '',
            ]);
        }

        $appointment = Appointment::withoutGlobalScopes()->create([
            'shop_id' => $shop->id,
            'customer_id' => $customer->id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addMinutes($totalDuration),
            'status' => 'scheduled',
            'price' => $totalPrice,
            'is_walkin' => false,
            'booked_online' => true,
        ]);

        $appointment->load(['staff:id,name,title', 'service:id,name,price,duration_minutes', 'shop:id,name,address,city,state']);

        return response()->json(['appointment' => $appointment], 201);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $customerIds = Customer::withoutGlobalScopes()
            ->where('email', $user->email)
            ->pluck('id');

        $appointment = Appointment::withoutGlobalScopes()
            ->whereIn('customer_id', $customerIds)
            ->where('status', 'scheduled')
            ->where('starts_at', '>', now())
            ->findOrFail($id);

        $appointment->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Appointment cancelled']);
    }
}
