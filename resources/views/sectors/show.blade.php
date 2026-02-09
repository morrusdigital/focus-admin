@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Sector Detail</h4>
        <a class="btn btn-light" href="{{ route('sectors.index') }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-2">{{ $sector->name }}</h5>
            <div class="mb-2"><strong>Slug:</strong> {{ $sector->slug }}</div>
            <div class="mb-2"><strong>Sort Order:</strong> {{ $sector->sort_order ?? '-' }}</div>
            <div class="mb-2">
                <strong>Status:</strong>
                @if ($sector->is_active)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Inactive</span>
                @endif
            </div>
            <div class="mb-2"><strong>Created:</strong> {{ $sector->created_at?->format('Y-m-d H:i') ?? '-' }}</div>
        </div>
    </div>
@endsection
