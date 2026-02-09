<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SectorController extends Controller
{
    public function index()
    {
        $sectors = Sector::orderByRaw('case when sort_order is null then 1 else 0 end')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('sectors.index', [
            'sectors' => $sectors,
            'title' => 'Sectors',
            'subtitle' => 'Data',
        ]);
    }

    public function create()
    {
        return view('sectors.create', [
            'sector' => new Sector(),
            'title' => 'Sectors',
            'subtitle' => 'Create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules());
        $validated['slug'] = $this->resolveSlug(
            $validated['slug'] ?? '',
            $validated['name'] ?? ''
        );
        $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        $validated['is_active'] = $request->boolean('is_active');

        Sector::create($validated);

        return redirect()
            ->route('sectors.index')
            ->with('success', 'Sector created.');
    }

    public function show(Sector $sector)
    {
        return view('sectors.show', [
            'sector' => $sector,
            'title' => 'Sectors',
            'subtitle' => 'Detail',
        ]);
    }

    public function edit(Sector $sector)
    {
        return view('sectors.edit', [
            'sector' => $sector,
            'title' => 'Sectors',
            'subtitle' => 'Edit',
        ]);
    }

    public function update(Request $request, Sector $sector)
    {
        $validated = $request->validate($this->updateRules($sector));
        $validated['slug'] = $this->resolveSlug(
            $validated['slug'] ?? '',
            $validated['name'] ?? '',
            $sector
        );
        $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        $validated['is_active'] = $request->boolean('is_active');

        $sector->update($validated);

        return redirect()
            ->route('sectors.index')
            ->with('success', 'Sector updated.');
    }

    public function destroy(Sector $sector)
    {
        $sector->delete();

        return redirect()
            ->route('sectors.index')
            ->with('success', 'Sector deleted.');
    }

    private function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:sectors,slug'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    private function updateRules(Sector $sector): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('sectors', 'slug')->ignore($sector->id)],
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

    private function resolveSlug(string $slug, string $name, ?Sector $sector = null): string
    {
        $base = $slug !== '' ? $slug : $name;
        $base = Str::slug($base);
        $base = $base !== '' ? $base : Str::slug(Str::random(8));

        $candidate = $base;
        $counter = 2;

        while (Sector::where('slug', $candidate)
            ->when($sector, fn ($query) => $query->where('id', '!=', $sector->id))
            ->exists()) {
            $candidate = $base.'-'.$counter;
            $counter++;
        }

        return $candidate;
    }
}
