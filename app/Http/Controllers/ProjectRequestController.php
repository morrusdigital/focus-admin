<?php

namespace App\Http\Controllers;

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

        $query = ProjectRequest::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('created_at');

        $projectRequests = $query->paginate(15)->withQueryString();

        return view('project-requests.index', [
            'projectRequests' => $projectRequests,
            'title' => 'Project Requests',
            'subtitle' => 'Inbox',
        ]);
    }

    public function show(ProjectRequest $projectRequest)
    {
        return view('project-requests.show', [
            'projectRequest' => $projectRequest,
            'title' => 'Project Requests',
            'subtitle' => 'Detail',
        ]);
    }

    public function update(Request $request, ProjectRequest $projectRequest)
    {
        $validated = $request->validate($this->updateRules());

        $projectRequest->update($validated);

        return redirect()
            ->route('project-requests.show', $projectRequest)
            ->with('success', 'Project request updated.');
    }

    public function destroy(ProjectRequest $projectRequest)
    {
        $this->deleteImages($projectRequest->project_images ?? []);
        $projectRequest->delete();

        return redirect()
            ->route('project-requests.index')
            ->with('success', 'Project request deleted.');
    }

    private function updateRules(): array
    {
        return [
            'status' => ['required', Rule::in(['new', 'in_progress', 'done'])],
            'notes' => ['nullable', 'string'],
        ];
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
