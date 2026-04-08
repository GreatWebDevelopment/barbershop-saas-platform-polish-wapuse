<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Staff;
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

        return Inertia::render('Appointments/Index', [
            'appointments' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
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
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $startsAt = \Carbon\Carbon::parse($validated['starts_at']);

        Appointment::create([
            ...$validated,
            'price' => $service->price,
            'ends_at' => $startsAt->copy()->addMinutes($service->duration_minutes),
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
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $startsAt = \Carbon\Carbon::parse($validated['starts_at']);

        $appointment->update([
            ...$validated,
            'price' => $service->price,
            'ends_at' => $startsAt->copy()->addMinutes($service->duration_minutes),
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted.');
    }
}
