<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreLocationRequest;
use App\Http\Requests\Backend\UpdateLocationRequest;
use App\Models\Location;
use App\Models\User;

class LocationController extends Controller
{
    public function index()
    {
        $this->authorizeOwner();
        $locations = Location::withCount('staff')->get();

        return view('admin.location.index', compact('locations'));
    }

    public function create()
    {
        $this->authorizeOwner();
        $staff = User::whereHas('roles', fn ($q) => $q->where('slug', 'staff'))->get();

        return view('admin.location.create', compact('staff'));
    }

    public function store(StoreLocationRequest $request)
    {
        $location = Location::create($request->validated());

        if ($request->has('staff')) {
            $location->staff()->sync($request->staff);
        }

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    public function edit(Location $location)
    {
        $this->authorizeOwner();
        $staff = User::whereHas('roles', fn ($q) => $q->where('slug', 'staff'))->get();
        $location->load('staff');

        return view('admin.location.edit', compact('location', 'staff'));
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        $location->update($request->validated());

        if ($request->has('staff')) {
            $location->staff()->sync($request->staff);
        } else {
            $location->staff()->detach();
        }

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        $this->authorizeOwner();

        // Prevent deletion if appointments exist
        if ($location->appointments()->count() > 0) {
            return back()->with('error', 'Cannot delete location with existing appointments. Please disable it instead.');
        }

        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }

    private function authorizeOwner(): void
    {
        if (! auth()->user()->hasRole('owner')) {
            abort(403, 'Unauthorized action. Owners only.');
        }
    }
}
