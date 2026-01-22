<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeaturedProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeaturedProjectController extends Controller
{
    public function index()
    {
        $projects = FeaturedProject::orderByRaw('case when sort_order is null then 1 else 0 end')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules($request));

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('featured-projects', 'public');
        }
        $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        if ($validated['sort_order'] === null) {
            $validated['sort_order'] = $this->nextSortOrder();
        }

        $project = FeaturedProject::create($validated);

        return response()->json([
            'data' => $project,
        ], 201);
    }

    public function show(FeaturedProject $featuredProject)
    {
        return response()->json([
            'data' => $featuredProject,
        ]);
    }

    public function update(Request $request, FeaturedProject $featuredProject)
    {
        $validated = $request->validate($this->updateRules($request));

        if ($request->hasFile('image')) {
            $this->deleteImageIfLocal($featuredProject->image);
            $validated['image'] = $request->file('image')->store('featured-projects', 'public');
        }
        if ($request->has('sort_order')) {
            $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        }

        $featuredProject->update($validated);

        return response()->json([
            'data' => $featuredProject->fresh(),
        ]);
    }

    public function destroy(FeaturedProject $featuredProject)
    {
        $this->deleteImageIfLocal($featuredProject->image);
        $featuredProject->delete();

        return response()->noContent();
    }

    private function storeRules(Request $request): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'scope' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'size' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = ['required', 'image', 'max:2048'];
        } else {
            $rules['image'] = ['required', 'string', 'max:2048'];
        }

        return $rules;
    }

    private function updateRules(Request $request): array
    {
        $rules = [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'scope' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'size' => ['sometimes', 'required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = ['nullable', 'image', 'max:2048'];
        } else {
            $rules['image'] = ['sometimes', 'required', 'string', 'max:2048'];
        }

        return $rules;
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
}
