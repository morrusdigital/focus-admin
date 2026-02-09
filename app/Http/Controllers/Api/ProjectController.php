<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'sector' => ['sometimes', 'filled', 'string', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first() ?? 'Invalid request.',
            ], 422);
        }

        $sector = $validator->validated()['sector'] ?? null;

        if ($sector) {
            $isValidSector = Sector::query()
                ->where('slug', $sector)
                ->exists();

            if (!$isValidSector) {
                return response()->json([
                    'message' => 'Invalid sector.',
                ], 422);
            }
        }

        $projects = Project::where('is_active', true)
            ->when($sector, function ($query) use ($sector) {
                $query->whereHas('sectors', fn ($inner) => $inner->where('slug', $sector));
            })
            ->with(['images', 'sectors'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Project $project) => $this->publicPayload($project));

        return response()->json([
            'data' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules());

        $validated['is_active'] = $request->has('is_active')
            ? $request->boolean('is_active')
            : true;

        $project = Project::create($validated);
        $project->sectors()->sync($validated['sectors'] ?? []);
        $this->storeImage($project, $request->file('image'));

        return response()->json([
            'data' => $this->payload($project),
        ], 201);
    }

    public function show(Project $project)
    {
        if (!$project->is_active) {
            abort(404);
        }

        $project->load(['images', 'sectors']);

        return response()->json([
            'data' => $this->payload($project),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate($this->updateRules());

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        $project->update($validated);
        $project->sectors()->sync($validated['sectors'] ?? []);
        if ($request->hasFile('image')) {
            $this->deleteAllImages($project);
            $this->storeImage($project, $request->file('image'));
        }

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
            'sectors' => ['required', 'array', 'min:1'],
            'sectors.*' => ['integer', 'exists:sectors,id'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['required', 'image', 'max:2048'],
        ];
    }

    private function updateRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'sectors' => ['required', 'array', 'min:1'],
            'sectors.*' => ['integer', 'exists:sectors,id'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    private function storeImage(Project $project, $file): void
    {
        if (!$file) {
            return;
        }

        $path = $file->store('projects', 'public');
        ProjectImage::create([
            'project_id' => $project->id,
            'image' => $path,
            'sort_order' => 1,
        ]);
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
        $project->loadMissing(['images', 'sectors']);

        return [
            'id' => $project->id,
            'title' => $project->title,
            'is_active' => (bool) $project->is_active,
            'sectors' => $project->sectors->map(function (Sector $sector) {
                return [
                    'id' => $sector->id,
                    'name' => $sector->name,
                    'slug' => $sector->slug,
                ];
            })->all(),
            'images' => $project->images->take(1)->map(function (ProjectImage $image) {
                return [
                    'id' => $image->id,
                    'image' => $image->image,
                    'image_url' => $image->image_url,
                    'sort_order' => $image->sort_order,
                ];
            })->all(),
        ];
    }

    private function publicPayload(Project $project): array
    {
        $project->loadMissing(['images', 'sectors']);

        return [
            'id' => $project->id,
            'title' => $project->title,
            'is_active' => (bool) $project->is_active,
            'sectors' => $project->sectors->map(function (Sector $sector) {
                return [
                    'id' => $sector->id,
                    'name' => $sector->name,
                    'slug' => $sector->slug,
                ];
            })->all(),
            'images' => $project->images->take(1)->map(function (ProjectImage $image) {
                return [
                    'id' => $image->id,
                    'image_url' => $image->image_url,
                    'image' => $image->image,
                    'sort_order' => $image->sort_order,
                ];
            })->all(),
        ];
    }
}
