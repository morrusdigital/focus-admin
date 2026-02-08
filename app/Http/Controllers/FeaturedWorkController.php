<?php

namespace App\Http\Controllers;

use App\Models\FeaturedWork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeaturedWorkController extends Controller
{
    public function index()
    {
        $featuredWorks = FeaturedWork::orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('featured-works.index', [
            'featuredWorks' => $featuredWorks,
            'title' => 'Featured Work',
            'subtitle' => 'Data',
        ]);
    }

    public function create()
    {
        return view('featured-works.create', [
            'featuredWork' => new FeaturedWork(),
            'title' => 'Featured Work',
            'subtitle' => 'Create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('featured-work', 'public');
        }

        $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        $validated['is_active'] = $request->boolean('is_active');

        FeaturedWork::create($validated);

        return redirect()
            ->route('featured-works.index')
            ->with('success', 'Featured work created.');
    }

    public function show(FeaturedWork $featuredWork)
    {
        return view('featured-works.show', [
            'featuredWork' => $featuredWork,
            'title' => 'Featured Work',
            'subtitle' => 'Detail',
        ]);
    }

    public function edit(FeaturedWork $featuredWork)
    {
        return view('featured-works.edit', [
            'featuredWork' => $featuredWork,
            'title' => 'Featured Work',
            'subtitle' => 'Edit',
        ]);
    }

    public function update(Request $request, FeaturedWork $featuredWork)
    {
        $validated = $request->validate($this->rules());

        if ($request->hasFile('image')) {
            $this->deleteImageIfLocal($featuredWork->image);
            $validated['image'] = $request->file('image')->store('featured-work', 'public');
        }

        $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        $validated['is_active'] = $request->boolean('is_active');

        $featuredWork->update($validated);

        return redirect()
            ->route('featured-works.index')
            ->with('success', 'Featured work updated.');
    }

    public function destroy(FeaturedWork $featuredWork)
    {
        $this->deleteImageIfLocal($featuredWork->image);
        $featuredWork->delete();

        return redirect()
            ->route('featured-works.index')
            ->with('success', 'Featured work deleted.');
    }

    private function rules(): array
    {
        return [
            'sector_slug' => ['required', 'string', 'max:120'],
            'sector_label' => ['required', 'string', 'max:150'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'cta_label' => ['nullable', 'string', 'max:150'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    private function normalizeSortOrder(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (int) $value;
    }

    private function deleteImageIfLocal(?string $image): void
    {
        if (!$image) {
            return;
        }

        if (Str::startsWith($image, ['http://', 'https://', '/'])) {
            return;
        }

        Storage::disk('public')->delete($image);
    }
}
