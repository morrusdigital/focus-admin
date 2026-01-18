@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Client Detail</h4>
        <a class="btn btn-light" href="{{ route('clients.index') }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h5 class="mb-2">{{ $client->name }}</h5>
                    <div class="mb-2"><strong>Sort Order:</strong> {{ $client->sort_order ?? '-' }}</div>
                    <div class="mb-2">
                        <strong>Status:</strong>
                        @if ($client->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                    <div class="mb-2">
                        <strong>Logo:</strong>
                        <a href="{{ $client->logo_url }}" target="_blank" rel="noopener">{{ $client->logo_url }}</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    @if ($client->logo_url)
                        <img class="img-fluid rounded" src="{{ $client->logo_url }}" alt="{{ $client->name }}">
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
