<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreServiceCategoryRequest;
use App\Http\Requests\Backend\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class ServiceCategoryController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', ServiceCategory::class);

        $categories = ServiceCategory::withCount('services')
            ->orderBy('sort_order')
            ->get();

        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', ServiceCategory::class);

        return view('admin.category.create');
    }

    public function store(StoreServiceCategoryRequest $request)
    {
        $this->authorize('create', ServiceCategory::class);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        ServiceCategory::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function show(ServiceCategory $category)
    {
        $this->authorize('viewDetail', $category);

        $category->load(['services' => function ($query) {
            $query->orderBy('sort_order');
        }]);

        return view('admin.category.details', compact('category'));
    }

    public function edit(ServiceCategory $category)
    {
        $this->authorize('update', $category);

        return view('admin.category.edit', compact('category'));
    }

    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $category)
    {
        $this->authorize('update', $category);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(ServiceCategory $category)
    {
        $this->authorize('delete', $category);

        // Prevent deletion if services are attached
        if ($category->services()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete this category because it contains active services. Please reassign or delete the services first.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
