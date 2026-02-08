@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Project Requests</h4>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row gy-2 gx-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or email" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="new" @selected(request('status') === 'new')>New</option>
                        <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
                        <option value="done" @selected(request('status') === 'done')>Done</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <button class="btn btn-primary" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('project-requests.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projectRequests as $projectRequest)
                            <tr>
                                <td>{{ $projectRequest->name }}</td>
                                <td>{{ $projectRequest->company }}</td>
                                <td>{{ $projectRequest->email }}</td>
                                <td>{{ $projectRequest->phone ?? '-' }}</td>
                                <td>{{ $projectRequest->project_location }}</td>
                                <td>
                                    @if ($projectRequest->status === 'done')
                                        <span class="badge bg-success">Done</span>
                                    @elseif ($projectRequest->status === 'in_progress')
                                        <span class="badge bg-warning text-dark">In Progress</span>
                                    @else
                                        <span class="badge bg-secondary">New</span>
                                    @endif
                                </td>
                                <td>{{ $projectRequest->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('project-requests.show', $projectRequest) }}">Detail</a>
                                    <form class="d-inline" method="POST" action="{{ route('project-requests.destroy', $projectRequest) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Delete this request?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $projectRequests->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
