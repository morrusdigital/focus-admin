<?php

namespace App\Http\Controllers;

use App\Models\FeaturedProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeaturedProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = FeaturedProject::orderByRaw('case when sort_order is null then 1 else 0 end')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('featured-projects.index', [
            'projects' => $projects,
            'title' => 'Featured Projects',
            'subtitle' => 'Data',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('featured-projects.create', [
            'project' => new FeaturedProject(),
            'title' => 'Featured Projects',
            'subtitle' => 'Create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules());

        $validated['image'] = $request->file('image')->store('featured-projects', 'public');
        $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        if ($validated['sort_order'] === null) {
            $validated['sort_order'] = $this->nextSortOrder();
        }

        FeaturedProject::create($validated);

        return redirect()
            ->route('featured-projects.index')
            ->with('success', 'Project created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FeaturedProject $featuredProject)
    {
        return view('featured-projects.show', [
            'project' => $featuredProject,
            'title' => 'Featured Projects',
            'subtitle' => 'Detail',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeaturedProject $featuredProject)
    {
        return view('featured-projects.edit', [
            'project' => $featuredProject,
            'title' => 'Featured Projects',
            'subtitle' => 'Edit',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FeaturedProject $featuredProject)
    {
        $validated = $request->validate($this->updateRules());

        if ($request->hasFile('image')) {
            $this->deleteImageIfLocal($featuredProject->image);
            $validated['image'] = $request->file('image')->store('featured-projects', 'public');
        } else {
            unset($validated['image']);
        }

        if ($request->has('sort_order')) {
            $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        }

        $featuredProject->update($validated);

        return redirect()
            ->route('featured-projects.index')
            ->with('success', 'Project updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeaturedProject $featuredProject)
    {
        $this->deleteImageIfLocal($featuredProject->image);
        $featuredProject->delete();

        return redirect()
            ->route('featured-projects.index')
            ->with('success', 'Project deleted.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:featured_projects,id'],
        ]);

        foreach ($validated['order'] as $index => $id) {
            FeaturedProject::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'OK']);
    }

    private function storeRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'scope' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['required', 'image', 'max:2048'],
            'size' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }

    private function updateRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'scope' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'size' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }

    private function normalizeSortOrder(?string $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function nextSortOrder(): int
    {
        return (int) (FeaturedProject::max('sort_order') ?? 0) + 1;
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
