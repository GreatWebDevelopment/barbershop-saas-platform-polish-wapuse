<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('category')->withCount('appointments');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->category) {
            $query->where('service_category_id', $request->category);
        }

        return Inertia::render('Services/Index', [
            'services' => $query->orderBy('service_category_id')->orderBy('name')->get(),
            'categories' => ServiceCategory::orderBy('sort_order')->get(),
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Services/Create', [
            'categories' => ServiceCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_category_id' => 'required|exists:service_categories,id',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5',
            'status' => 'required|in:active,inactive',
        ]);

        Service::create([...$validated, 'shop_id' => 1]);

        return redirect()->route('services.index')->with('success', 'Service created.');
    }

    public function edit(Service $service)
    {
        return Inertia::render('Services/Edit', [
            'service' => $service->load('category'),
            'categories' => ServiceCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_category_id' => 'required|exists:service_categories,id',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5',
            'status' => 'required|in:active,inactive',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted.');
    }
}
