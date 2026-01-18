<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Client $client) => $this->payload($client));

        return response()->json([
            'data' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules());

        $validated['logo'] = $request->file('logo')->store('clients', 'public');
        $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        $validated['is_active'] = $request->has('is_active')
            ? $request->boolean('is_active')
            : true;

        $client = Client::create($validated);

        return response()->json([
            'data' => $this->payload($client),
        ], 201);
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate($this->updateRules());

        if ($request->hasFile('logo')) {
            $this->deleteLogoIfLocal($client->logo);
            $validated['logo'] = $request->file('logo')->store('clients', 'public');
        }

        if ($request->has('sort_order')) {
            $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        }

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        $client->update($validated);

        return response()->json([
            'data' => $this->payload($client->fresh()),
        ]);
    }

    public function destroy(Client $client)
    {
        $this->deleteLogoIfLocal($client->logo);
        $client->delete();

        return response()->noContent();
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

    private function payload(Client $client): array
    {
        return [
            'id' => $client->id,
            'name' => $client->name,
            'logo' => $client->logo,
            'logo_url' => $client->logo_url,
            'sort_order' => $client->sort_order,
            'is_active' => (bool) $client->is_active,
        ];
    }
}
