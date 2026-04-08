<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Services/Categories', [
            'categories' => ServiceCategory::withCount('services')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'integer|min:0',
        ]);

        ServiceCategory::create($validated);

        return redirect()->route('service-categories.index')->with('success', 'Category created.');
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'integer|min:0',
        ]);

        $serviceCategory->update($validated);

        return redirect()->route('service-categories.index')->with('success', 'Category updated.');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        if ($serviceCategory->services()->count() > 0) {
            return back()->withErrors(['category' => 'Cannot delete a category that has services.']);
        }

        $serviceCategory->delete();
        return redirect()->route('service-categories.index')->with('success', 'Category deleted.');
    }
}
