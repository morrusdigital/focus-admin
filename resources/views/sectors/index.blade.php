@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Sectors</h4>
        <a class="btn btn-primary" href="{{ route('sectors.create') }}">Add Sector</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Sort</th>
                            <th>Active</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sectors as $sector)
                            <tr>
                                <td>{{ $sector->name }}</td>
                                <td>{{ $sector->slug }}</td>
                                <td>{{ $sector->sort_order ?? '-' }}</td>
                                <td>
                                    @if ($sector->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('sectors.show', $sector) }}">Detail</a>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('sectors.edit', $sector) }}">Edit</a>
                                    <form class="d-inline" method="POST" action="{{ route('sectors.destroy', $sector) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Delete this sector?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No sectors yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
