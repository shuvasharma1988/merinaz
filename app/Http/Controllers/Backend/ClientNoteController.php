<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreClientNoteRequest;
use App\Models\ClientNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ClientNoteController extends Controller
{
    public function store(StoreClientNoteRequest $request)
    {
        Gate::authorize('create', ClientNote::class);

        ClientNote::create([
            'client_id' => $request->client_id,
            'staff_id' => auth()->id(),
            'note' => $request->note,
            'visibility' => $request->visibility,
        ]);

        return back()->with('success', 'Client note added successfully.');
    }

    public function update(Request $request, ClientNote $clientNote)
    {
        Gate::authorize('update', $clientNote);

        $validated = $request->validate([
            'note' => 'required|string|min:3',
            'visibility' => 'required|in:private,team,manager',
        ]);

        $clientNote->update($validated);

        return back()->with('success', 'Client note updated successfully.');
    }

    public function destroy(ClientNote $clientNote)
    {
        Gate::authorize('delete', $clientNote);
        $clientNote->delete();

        return back()->with('success', 'Client note deleted successfully.');
    }
}
