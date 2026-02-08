@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Project Request Detail</h4>
        <a class="btn btn-light" href="{{ route('project-requests.index') }}">Back</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <h5 class="mb-2">{{ $projectRequest->name }}</h5>
                    <div class="mb-2"><strong>Company:</strong> {{ $projectRequest->company ?: '-' }}</div>
                    <div class="mb-2"><strong>Email:</strong> {{ $projectRequest->email ?: '-' }}</div>
                    <div class="mb-2"><strong>Phone:</strong> {{ $projectRequest->phone ?: '-' }}</div>
                    <div class="mb-2"><strong>Location:</strong> {{ $projectRequest->project_location }}</div>
                    <div class="mb-2"><strong>Area Estimate:</strong> {{ $projectRequest->area_estimate }}</div>
                    <div class="mb-2"><strong>Timeline:</strong> {{ $projectRequest->timeline ?: '-' }}</div>
                    <div class="mb-2"><strong>Description:</strong></div>
                    <p class="text-muted">{{ $projectRequest->project_description ?: '-' }}</p>
                    <div class="mb-2">
                        <strong>Status:</strong>
                        @if ($projectRequest->status === 'done')
                            <span class="badge bg-success">Done</span>
                        @elseif ($projectRequest->status === 'in_progress')
                            <span class="badge bg-warning text-dark">In Progress</span>
                        @else
                            <span class="badge bg-secondary">New</span>
                        @endif
                    </div>
                    <div class="mb-2"><strong>Notes:</strong> {{ $projectRequest->notes ?? '-' }}</div>
                    <div class="mb-2"><strong>Created:</strong> {{ $projectRequest->created_at?->format('Y-m-d H:i') ?? '-' }}</div>
                </div>
                <div class="col-lg-4">
                    @if (!empty($projectRequest->project_images_urls))
                        <div class="row g-2">
                            @foreach ($projectRequest->project_images_urls as $imageUrl)
                                @php
                                    $path = parse_url($imageUrl, PHP_URL_PATH) ?? '';
                                    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                    $fileName = basename($path) ?: 'File';
                                @endphp
                                <div class="col-6">
                                    <a href="{{ $imageUrl }}" target="_blank" rel="noopener" class="d-block">
                                        @if (in_array($extension, ['jpg', 'jpeg', 'png'], true))
                                            <img class="img-fluid rounded" src="{{ $imageUrl }}" alt="{{ $projectRequest->name }}">
                                        @else
                                            <div class="border rounded p-2 text-truncate">{{ $fileName }}</div>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No images.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="mb-3">Update Status</h5>
            <form method="POST" action="{{ route('project-requests.update', $projectRequest) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="new" @selected(old('status', $projectRequest->status) === 'new')>New</option>
                            <option value="in_progress" @selected(old('status', $projectRequest->status) === 'in_progress')>In Progress</option>
                            <option value="done" @selected(old('status', $projectRequest->status) === 'done')>Done</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $projectRequest->notes) }}</textarea>
                    </div>
                </div>
                <button class="btn btn-primary mt-3" type="submit">Save</button>
            </form>
        </div>
    </div>
@endsection
