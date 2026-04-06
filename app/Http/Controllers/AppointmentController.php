<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['customer', 'staff', 'service'])
            ->orderByDesc('starts_at');

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('customer', fn ($q) => $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->tab) {
            $now = Carbon::now();
            match ($request->tab) {
                'today' => $query->whereDate('starts_at', $now->toDateString()),
                'upcoming' => $query->where('starts_at', '>', $now),
                'past' => $query->where('starts_at', '<', $now),
                default => null,
            };
        }

        return Inertia::render('Appointments/Index', [
            'appointments' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only(['search', 'status', 'tab']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Appointments/Create', [
            'customers' => Customer::orderBy('first_name')->get(),
            'staff' => Staff::where('status', 'active')->get(),
            'services' => Service::with('category')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'staff_id' => 'required|exists:staff,id',
            'service_id' => 'required|exists:services,id',
            'starts_at' => 'required|date',
            'status' => 'required|in:pending,confirmed,completed,cancelled,no-show',
            'is_walkin' => 'boolean',
            'notes' => 'nullable|string',
            'payment_method' => 'nullable|in:cash,card,paypal',
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $startsAt = Carbon::parse($validated['starts_at']);

        $paymentMethod = $validated['payment_method'] ?? null;
        unset($validated['payment_method']);

        Appointment::create([
            ...$validated,
            'shop_id' => 1,
            'price' => $service->price,
            'ends_at' => $startsAt->copy()->addMinutes($service->duration_minutes),
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentMethod === 'cash' ? 'paid' : 'pending',
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment created.');
    }

    public function edit(Appointment $appointment)
    {
        $appointment->load(['customer', 'staff', 'service']);

        return Inertia::render('Appointments/Edit', [
            'appointment' => $appointment,
            'customers' => Customer::orderBy('first_name')->get(),
            'staff' => Staff::where('status', 'active')->get(),
            'services' => Service::with('category')->get(),
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'staff_id' => 'required|exists:staff,id',
            'service_id' => 'required|exists:services,id',
            'starts_at' => 'required|date',
            'status' => 'required|in:pending,confirmed,completed,cancelled,no-show',
            'is_walkin' => 'boolean',
            'notes' => 'nullable|string',
            'payment_method' => 'nullable|in:cash,card,paypal',
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $startsAt = Carbon::parse($validated['starts_at']);

        $paymentMethod = $validated['payment_method'] ?? $appointment->payment_method;
        unset($validated['payment_method']);

        $appointment->update([
            ...$validated,
            'price' => $service->price,
            'ends_at' => $startsAt->copy()->addMinutes($service->duration_minutes),
            'payment_method' => $paymentMethod,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted.');
    }
}
