<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreClientRequest;
use App\Http\Requests\Backend\UpdateClientRequest;
use App\Models\ClientProfile;
use App\Models\User;
use App\Services\Backend\ClientService;
use Illuminate\Support\Facades\Gate;

class ClientController extends Controller
{
    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index()
    {
        Gate::authorize('viewAny', ClientProfile::class);

        $clients = User::whereHas('clientProfile')
            ->with('clientProfile')
            ->latest()
            ->paginate(15);

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        Gate::authorize('create', ClientProfile::class);

        return view('admin.clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        Gate::authorize('create', ClientProfile::class);

        $this->clientService->createClient($request->validated());

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client profile created successfully.');
    }

    public function show(User $client)
    {
        // Load profile or fail
        $profile = $client->clientProfile ?? new ClientProfile;
        Gate::authorize('view', $profile);

        $client->load([
            'clientProfile',
            'appointments' => fn ($q) => $q->latest()->take(10),
        ]);

        // Load notes based on policy visibility
        $notes = $client->clientNotes()->with('staff')->latest()->get()
            ->filter(fn ($note) => auth()->user()->can('view', $note));

        return view('admin.clients.details', compact('client', 'notes'));
    }

    public function edit(User $client)
    {
        $profile = $client->clientProfile ?? new ClientProfile;
        Gate::authorize('update', $profile);

        $client->load('clientProfile');

        return view('admin.clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, User $client)
    {
        $profile = $client->clientProfile ?? new ClientProfile;
        Gate::authorize('update', $profile);

        $this->clientService->updateClient($client, $request->validated());

        return redirect()->route('admin.clients.show', $client)
            ->with('success', 'Client profile updated successfully.');
    }

    public function destroy(User $client)
    {
        $profile = $client->clientProfile;
        if ($profile) {
            Gate::authorize('delete', $profile);
            $profile->delete();
        }

        // Optionally soft delete the user account too, depending on business rules.
        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
