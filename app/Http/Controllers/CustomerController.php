<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Staff;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"));
        }

        $sortField = $request->input('sort', 'first_name');
        $sortDir = $request->input('direction', 'asc');
        $allowedSorts = ['first_name', 'last_visit_at', 'total_spent', 'loyalty_points', 'visit_count'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('first_name');
        }

        return Inertia::render('Customers/Index', [
            'customers' => $query->paginate(24)->withQueryString(),
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Customers/Create', [
            'staff' => Staff::where('status', 'active')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'preferred_stylist_id' => 'nullable|exists:staff,id',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer added.');
    }

    public function edit(Customer $customer)
    {
        $customer->load(['preferredStylist', 'appointments' => function ($q) {
            $q->with(['staff', 'service'])->orderByDesc('starts_at')->limit(20);
        }]);

        return Inertia::render('Customers/Edit', [
            'customer' => $customer,
            'staff' => Staff::where('status', 'active')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'preferred_stylist_id' => 'nullable|exists:staff,id',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}
