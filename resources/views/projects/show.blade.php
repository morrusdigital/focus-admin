@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Project Detail</h4>
        <a class="btn btn-light" href="{{ route('projects.index') }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <h5 class="mb-2">{{ $project->title }}</h5>
                    <p class="text-muted mb-3">{{ $project->description }}</p>
                    <div class="mb-2"><strong>Sector:</strong> {{ $project->sector }}</div>
                    <div class="mb-2"><strong>Status:</strong> {{ $project->status }}</div>
                    <div class="mb-2"><strong>Location:</strong> {{ $project->location }}</div>
                    <div class="mb-2"><strong>Badge:</strong> {{ $project->badge }}</div>
                    <div class="mb-2"><strong>Sort Order:</strong> {{ $project->sort_order ?? '-' }}</div>
                    <div class="mb-2">
                        <strong>Active:</strong>
                        @if ($project->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    @if ($project->images->isNotEmpty())
                        <div class="row g-2">
                            @foreach ($project->images as $image)
                                <div class="col-6">
                                    <a href="{{ $image->image_url }}" target="_blank" rel="noopener">
                                        <img class="img-fluid rounded" src="{{ $image->image_url }}" alt="{{ $project->title }}">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
