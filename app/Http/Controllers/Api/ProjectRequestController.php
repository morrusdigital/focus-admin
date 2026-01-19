<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectRequestResource;
use App\Models\ProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? $perPage : 10;

        $query = ProjectRequest::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('created_at');

        $projectRequests = $query->paginate($perPage)->withQueryString();

        return ProjectRequestResource::collection($projectRequests)
            ->additional([
                'meta' => [
                    'pagination' => [
                        'current_page' => $projectRequests->currentPage(),
                        'last_page' => $projectRequests->lastPage(),
                        'per_page' => $projectRequests->perPage(),
                        'total' => $projectRequests->total(),
                    ],
                ],
            ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules());
        $validated['project_images'] = $this->storeImages($request->file('project_images', []));
        $validated['status'] = 'new';

        $projectRequest = ProjectRequest::create($validated);

        return response()->json([
            'message' => 'OK',
            'data' => (new ProjectRequestResource($projectRequest))->resolve(),
        ], 201);
    }

    public function show(ProjectRequest $projectRequest)
    {
        return new ProjectRequestResource($projectRequest);
    }

    public function update(Request $request, ProjectRequest $projectRequest)
    {
        $validated = $request->validate($this->updateRules());

        $projectRequest->update($validated);

        return new ProjectRequestResource($projectRequest->fresh());
    }

    public function destroy(ProjectRequest $projectRequest)
    {
        $this->deleteImages($projectRequest->project_images ?? []);
        $projectRequest->delete();

        return response()->noContent();
    }

    private function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'project_location' => ['required', 'string', 'max:255'],
            'area_estimate' => ['required', 'string', 'max:255'],
            'timeline' => ['required', 'string', 'max:255'],
            'project_description' => ['required', 'string'],
            'project_images' => ['nullable', 'array', 'max:5'],
            'project_images.*' => ['image', 'max:5120'],
        ];
    }

    private function updateRules(): array
    {
        return [
            'status' => ['required', Rule::in(['new', 'in_progress', 'done'])],
            'notes' => ['nullable', 'string'],
        ];
    }

    private function storeImages(array $files): ?array
    {
        if (empty($files)) {
            return null;
        }

        $paths = [];

        foreach ($files as $file) {
            $paths[] = $file->store('project-requests', 'public');
        }

        return $paths;
    }

    private function deleteImages(array $images): void
    {
        foreach ($images as $image) {
            if (!$image) {
                continue;
            }

            if (Str::startsWith($image, ['http://', 'https://', '/'])) {
                continue;
            }

            Storage::disk('public')->delete($image);
        }
    }
}
