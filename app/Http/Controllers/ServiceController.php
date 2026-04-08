<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceAddOn;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('category', 'addOns');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->category) {
            $query->where('service_category_id', $request->category);
        }

        return Inertia::render('Services/Index', [
            'services' => $query->orderBy('service_category_id')->orderBy('sort_order')->orderBy('name')->get(),
            'categories' => ServiceCategory::orderBy('sort_order')->get(),
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Services/Create', [
            'categories' => ServiceCategory::orderBy('sort_order')->get(),
            'addOns' => ServiceAddOn::orderBy('name')->get(),
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
            'skill_level' => 'required|in:junior,intermediate,master',
            'sort_order' => 'integer|min:0',
            'add_on_ids' => 'array',
            'add_on_ids.*' => 'exists:service_add_ons,id',
        ]);

        $addOnIds = $validated['add_on_ids'] ?? [];
        unset($validated['add_on_ids']);

        $service = Service::create($validated);
        $service->addOns()->sync($addOnIds);

        return redirect()->route('services.index')->with('success', 'Service created.');
    }

    public function edit(Service $service)
    {
        return Inertia::render('Services/Edit', [
            'service' => $service->load('category', 'addOns'),
            'categories' => ServiceCategory::orderBy('sort_order')->get(),
            'addOns' => ServiceAddOn::orderBy('name')->get(),
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
            'skill_level' => 'required|in:junior,intermediate,master',
            'sort_order' => 'integer|min:0',
            'add_on_ids' => 'array',
            'add_on_ids.*' => 'exists:service_add_ons,id',
        ]);

        $addOnIds = $validated['add_on_ids'] ?? [];
        unset($validated['add_on_ids']);

        $service->update($validated);
        $service->addOns()->sync($addOnIds);

        return redirect()->route('services.index')->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:services,id',
            'orders.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['orders'] as $item) {
            Service::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return back()->with('success', 'Order updated.');
    }
}
