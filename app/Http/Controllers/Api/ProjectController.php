<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('is_active', true)
            ->with('images')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Project $project) => $this->payload($project));

        return response()->json([
            'data' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules());

        $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        $validated['is_active'] = $request->has('is_active')
            ? $request->boolean('is_active')
            : true;

        $project = Project::create($validated);
        $this->storeImages($project, $request->file('images', []));

        return response()->json([
            'data' => $this->payload($project),
        ], 201);
    }

    public function show(Project $project)
    {
        if (!$project->is_active) {
            abort(404);
        }

        $project->load('images');

        return response()->json([
            'data' => $this->payload($project),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate($this->updateRules());

        if ($request->has('sort_order')) {
            $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        }

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        $project->update($validated);
        $removeIds = (array) $request->input('remove_image_ids', []);
        $this->removeImages($project, $removeIds);
        $this->storeImages($project, $request->file('images', []));

        return response()->json([
            'data' => $this->payload($project->fresh()),
        ]);
    }

    public function destroy(Project $project)
    {
        $this->deleteAllImages($project);
        $project->delete();

        return response()->noContent();
    }

    private function storeRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'sector' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'badge' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'max:2048'],
        ];
    }

    private function updateRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'sector' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'badge' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer'],
        ];
    }

    private function normalizeSortOrder(?string $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function storeImages(Project $project, array $files): void
    {
        if (empty($files)) {
            return;
        }

        $currentMax = $project->images()->max('sort_order') ?? 0;

        foreach ($files as $index => $file) {
            $path = $file->store('projects', 'public');
            ProjectImage::create([
                'project_id' => $project->id,
                'image' => $path,
                'sort_order' => $currentMax + $index + 1,
            ]);
        }
    }

    private function removeImages(Project $project, array $removeIds): void
    {
        if (empty($removeIds)) {
            return;
        }

        $images = $project->images()->whereIn('id', $removeIds)->get();

        foreach ($images as $image) {
            $this->deleteImageIfLocal($image->image);
            $image->delete();
        }
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

    private function deleteAllImages(Project $project): void
    {
        $project->load('images');

        foreach ($project->images as $image) {
            $this->deleteImageIfLocal($image->image);
        }

        $project->images()->delete();
    }

    private function payload(Project $project): array
    {
        $project->loadMissing('images');

        return [
            'id' => $project->id,
            'title' => $project->title,
            'sector' => $project->sector,
            'location' => $project->location,
            'status' => $project->status,
            'description' => $project->description,
            'badge' => $project->badge,
            'sort_order' => $project->sort_order,
            'is_active' => (bool) $project->is_active,
            'images' => $project->images->map(function (ProjectImage $image) {
                return [
                    'id' => $image->id,
                    'image' => $image->image,
                    'image_url' => $image->image_url,
                    'sort_order' => $image->sort_order,
                ];
            })->all(),
        ];
    }
}
