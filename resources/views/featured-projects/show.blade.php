@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Project Detail</h4>
        <a class="btn btn-light" href="{{ route('featured-projects.index') }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <h5 class="mb-2">{{ $project->title }}</h5>
                    <p class="text-muted mb-3">{{ $project->description }}</p>
                    <div class="mb-2"><strong>Scope:</strong> {{ $project->scope }}</div>
                    <div class="mb-2"><strong>Size:</strong> {{ $project->size }}</div>
                    <div class="mb-2">
                        <strong>Image:</strong>
                        <a href="{{ $project->image_url }}" target="_blank" rel="noopener">{{ $project->image_url }}</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <img class="img-fluid rounded" src="{{ $project->image_url }}" alt="{{ $project->title }}">
                </div>
            </div>
        </div>
    </div>
@endsection
