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

        Staff::create([...$validated, 'shop_id' => 1]);

        return redirect()->route('staff.index')->with('success', 'Staff member added.');
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
}
