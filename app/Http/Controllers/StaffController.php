<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::withCount('appointments');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        return Inertia::render('Staff/Index', [
            'staff' => $query->get(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Staff/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'years_experience' => 'integer|min:0',
            'hourly_rate' => 'numeric|min:0',
            'commission_percent' => 'integer|min:0|max:100',
            'specialties' => 'nullable|array',
            'bio' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Staff::create($validated);

        return redirect()->route('staff.index')->with('success', 'Staff member added.');
    }

    public function show(Staff $staff)
    {
        $staff->loadCount('appointments');

        return Inertia::render('Staff/Show', [
            'staffMember' => $staff,
        ]);
    }

    public function edit(Staff $staff)
    {
        return Inertia::render('Staff/Edit', ['staffMember' => $staff]);
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'years_experience' => 'integer|min:0',
            'hourly_rate' => 'numeric|min:0',
            'commission_percent' => 'integer|min:0|max:100',
            'specialties' => 'nullable|array',
            'bio' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Staff member updated.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff member removed.');
    }

    public function schedule(Staff $staff)
    {
        return Inertia::render('Staff/Schedule', [
            'staffMember' => $staff,
        ]);
    }

    public function updateSchedule(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'availability_schedule' => 'required|array',
            'availability_schedule.*.enabled' => 'required|boolean',
            'availability_schedule.*.blocks' => 'nullable|array',
            'availability_schedule.*.blocks.*.start' => 'required|string',
            'availability_schedule.*.blocks.*.end' => 'required|string',
            'availability_schedule.*.breaks' => 'nullable|array',
            'availability_schedule.*.breaks.*.start' => 'required|string',
            'availability_schedule.*.breaks.*.end' => 'required|string',
        ]);

        $staff->update($validated);

        return redirect()->back()->with('success', 'Schedule updated.');
    }

    public function commissions(Staff $staff)
    {
        $appointments = $staff->appointments()
            ->where('status', 'completed')
            ->with('service')
            ->orderBy('starts_at', 'desc')
            ->get()
            ->map(function ($appt) use ($staff) {
                $commission = ($appt->price * $staff->commission_percent) / 100;
                return [
                    'id' => $appt->id,
                    'date' => $appt->starts_at->format('Y-m-d'),
                    'service' => $appt->service?->name ?? 'N/A',
                    'price' => (float) $appt->price,
                    'commission' => round($commission, 2),
                ];
            });

        $now = now();

        $todayTotal = $appointments->where('date', $now->format('Y-m-d'))->sum('commission');
        $weekTotal = $appointments->filter(fn ($a) => $a['date'] >= $now->startOfWeek()->format('Y-m-d'))->sum('commission');
        $monthTotal = $appointments->filter(fn ($a) => str_starts_with($a['date'], $now->format('Y-m')))->sum('commission');

        return Inertia::render('Staff/Commissions', [
            'staffMember' => $staff,
            'appointments' => $appointments->values(),
            'totals' => [
                'daily' => round($todayTotal, 2),
                'weekly' => round($weekTotal, 2),
                'monthly' => round($monthTotal, 2),
                'all_time' => round($appointments->sum('commission'), 2),
            ],
        ]);
    }
}
