@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Company Profile Download Detail</h4>
        <a class="btn btn-light" href="{{ route('company-profile-downloads.index') }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-2">{{ $download->name }}</h5>
            <div class="mb-2"><strong>Phone:</strong> {{ $download->phone }}</div>
            <div class="mb-2"><strong>Domicile:</strong> {{ $download->domicile }}</div>
            <div class="mb-2"><strong>IP Address:</strong> {{ $download->ip_address ?? '-' }}</div>
            <div class="mb-2"><strong>User Agent:</strong> {{ $download->user_agent ?? '-' }}</div>
            <div class="mb-2"><strong>Downloaded At:</strong> {{ $download->downloaded_at?->format('Y-m-d H:i') ?? '-' }}</div>
            <div class="mb-2"><strong>Created At:</strong> {{ $download->created_at?->format('Y-m-d H:i') ?? '-' }}</div>
        </div>
    </div>
@endsection
