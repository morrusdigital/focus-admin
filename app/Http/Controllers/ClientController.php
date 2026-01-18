<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('clients.index', [
            'clients' => $clients,
            'title' => 'Clients',
            'subtitle' => 'Data',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create', [
            'client' => new Client(),
            'title' => 'Clients',
            'subtitle' => 'Create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules());
        $validated['logo'] = $request->file('logo')->store('clients', 'public');
        $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        $validated['is_active'] = $request->boolean('is_active');

        Client::create($validated);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return view('clients.show', [
            'client' => $client,
            'title' => 'Clients',
            'subtitle' => 'Detail',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', [
            'client' => $client,
            'title' => 'Clients',
            'subtitle' => 'Edit',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate($this->updateRules());

        if ($request->hasFile('logo')) {
            $this->deleteLogoIfLocal($client->logo);
            $validated['logo'] = $request->file('logo')->store('clients', 'public');
        }

        $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        $validated['is_active'] = $request->boolean('is_active');

        $client->update($validated);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $this->deleteLogoIfLocal($client->logo);
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client deleted.');
    }

    private function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['required', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    private function updateRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    private function normalizeSortOrder(?string $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function deleteLogoIfLocal(?string $logo): void
    {
        if (!$logo) {
            return;
        }

        if (Str::startsWith($logo, ['http://', 'https://', '/'])) {
            return;
        }

        Storage::disk('public')->delete($logo);
    }
}
