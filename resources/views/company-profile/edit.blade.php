@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Company Profile File</h4>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <strong>Current file:</strong>
                @if ($fileUrl)
                    <a href="{{ $fileUrl }}" target="_blank" rel="noopener">Download</a>
                @else
                    <span class="text-muted">Not set</span>
                @endif
            </div>

            <form method="POST" action="{{ route('company-profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Upload PDF</label>
                    <input type="file" name="company_profile" class="form-control" accept="application/pdf" required>
                    <small class="text-muted">PDF only, max 10MB.</small>
                    @error('company_profile')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <button class="btn btn-primary" type="submit">Save</button>
            </form>
        </div>
    </div>
@endsection
