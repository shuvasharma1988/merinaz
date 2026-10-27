<?php

namespace App\Http\Controllers\Backend;

use App\Events\Backend\ServiceModified;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreServiceRequest;
use App\Http\Requests\Backend\UpdateServiceRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Service::class);
        $categories = ServiceCategory::with(['services' => function ($query) {
            $query->orderBy('sort_order');
        }, 'services.staff'])->orderBy('sort_order')->get();

        return view('admin.service.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', Service::class);
        $categories = ServiceCategory::where('is_active', true)->get();

        // Updated: Fetch staff by querying the roles relationship using the 'slug' column
        $staffList = User::whereHas('roles', function ($query) {
            $query->whereIn('slug', ['owner', 'manager', 'staff']);
        })->get();

        return view('admin.service.create', compact('categories', 'staffList'));
    }

    public function store(StoreServiceRequest $request)
    {
        $this->authorize('create', Service::class);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service = Service::create($data);

        if ($request->has('staff_ids')) {
            $service->staff()->sync($request->staff_ids);
        }

        event(new ServiceModified($service, 'created'));

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function show(Service $service)
    {
        $this->authorize('viewDetail', $service);
        $this->authorize('viewDetail', $service->category);

        $service->load('category', 'staff');

        return view('admin.service.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $this->authorize('update', $service);
        $categories = ServiceCategory::where('is_active', true)->get();

        // Updated: Fetch staff by querying the roles relationship using the 'slug' column
        $staffList = User::whereHas('roles', function ($query) {
            $query->whereIn('slug', ['owner', 'manager', 'staff']);
        })->get();

        $service->load('staff');

        return view('admin.service.edit', compact('service', 'categories', 'staffList'));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $this->authorize('update', $service);

        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        if ($request->has('staff_ids')) {
            $service->staff()->sync($request->staff_ids);
        }

        event(new ServiceModified($service, 'updated'));

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);

        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        event(new ServiceModified($service, 'deleted'));
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }
}
