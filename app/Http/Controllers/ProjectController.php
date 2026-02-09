<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sector = trim((string) $request->query('sector', ''));

        $sectors = Sector::orderByRaw('case when sort_order is null then 1 else 0 end')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $projects = Project::with(['images', 'sectors'])
            ->when($sector !== '', function ($query) use ($sector) {
                $query->whereHas('sectors', fn ($inner) => $inner->where('slug', $sector));
            })
            ->orderByDesc('created_at')
            ->get();

        return view('projects.index', [
            'projects' => $projects,
            'sector' => $sector,
            'sectors' => $sectors,
            'title' => 'Projects',
            'subtitle' => 'Data',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.create', [
            'project' => new Project(),
            'sectors' => Sector::orderByRaw('case when sort_order is null then 1 else 0 end')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'selectedSectorIds' => old('sectors', []),
            'title' => 'Projects',
            'subtitle' => 'Create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules());
        $validated['is_active'] = $request->boolean('is_active');

        $project = Project::create($validated);
        $project->sectors()->sync($validated['sectors'] ?? []);
        $this->storeImages($project, $request->file('images', []));

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['images', 'sectors']);

        return view('projects.show', [
            'project' => $project,
            'title' => 'Projects',
            'subtitle' => 'Detail',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $project->load(['images', 'sectors']);

        return view('projects.edit', [
            'project' => $project,
            'sectors' => Sector::orderByRaw('case when sort_order is null then 1 else 0 end')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'selectedSectorIds' => old('sectors', $project->sectors->pluck('id')->all()),
            'title' => 'Projects',
            'subtitle' => 'Edit',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate($this->updateRules());

        $validated['is_active'] = $request->boolean('is_active');

        $project->update($validated);
        $project->sectors()->sync($validated['sectors'] ?? []);

        $removeIds = (array) $request->input('remove_image_ids', []);
        $this->removeImages($project, $removeIds);
        $this->storeImages($project, $request->file('images', []));

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->deleteAllImages($project);
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted.');
    }

    private function storeRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'sectors' => ['required', 'array', 'min:1'],
            'sectors.*' => ['integer', 'exists:sectors,id'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'max:2048'],
        ];
    }

    private function updateRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'sectors' => ['required', 'array', 'min:1'],
            'sectors.*' => ['integer', 'exists:sectors,id'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer'],
        ];
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

    private function deleteAllImages(Project $project): void
    {
        $project->load('images');

        foreach ($project->images as $image) {
            $this->deleteImageIfLocal($image->image);
        }

        $project->images()->delete();
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
