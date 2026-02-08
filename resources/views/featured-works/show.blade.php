@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Featured Work Detail</h4>
        <a class="btn btn-light" href="{{ route('featured-works.index') }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-8">
                    <h5 class="mb-2">{{ $featuredWork->title }}</h5>
                    <div class="mb-2"><strong>Sector:</strong> {{ $featuredWork->sector_label }} ({{ $featuredWork->sector_slug }})</div>
                    <div class="mb-2"><strong>Description:</strong></div>
                    <p class="text-muted">{{ $featuredWork->description }}</p>
                    <div class="mb-2"><strong>CTA Label:</strong> {{ $featuredWork->cta_label ?? '-' }}</div>
                    <div class="mb-2"><strong>CTA URL:</strong> {{ $featuredWork->cta_url ?? '-' }}</div>
                    <div class="mb-2"><strong>Sort Order:</strong> {{ $featuredWork->sort_order }}</div>
                    <div class="mb-2">
                        <strong>Status:</strong>
                        @if ($featuredWork->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    @if ($featuredWork->image_url)
                        <img class="img-fluid rounded" src="{{ $featuredWork->image_url }}" alt="{{ $featuredWork->title }}">
                    @else
                        <p class="text-muted">No image.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
