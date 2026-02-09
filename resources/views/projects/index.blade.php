@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Projects</h4>
        <a class="btn btn-primary" href="{{ route('projects.create') }}">Add Project</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row gy-2 gx-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Project title" value="{{ $search }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sector</label>
                    <select name="sector" class="form-select">
                        <option value="">All</option>
                        @foreach ($sectors as $item)
                            <option value="{{ $item->slug }}" @selected($sector === $item->slug)>
                                {{ $item->name }} ({{ $item->slug }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('projects.index') }}">Reset</a>
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
                            <th>Image</th>
                            <th>Title</th>
                            <th>Sectors</th>
                            <th>Active</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            <tr>
                                <td>
                                    @php
                                        $firstImage = $project->images->first();
                                    @endphp
                                    @if ($firstImage)
                                        <img src="{{ $firstImage->image_url }}" alt="{{ $project->title }}" style="height: 36px;" class="img-fluid">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $project->title }}</td>
                                <td>
                                    @forelse ($project->sectors as $sectorItem)
                                        <span class="badge bg-light text-dark border">{{ $sectorItem->name }}</span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                <td>
                                    @if ($project->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('projects.show', $project) }}">Detail</a>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('projects.edit', $project) }}">Edit</a>
                                    <form class="d-inline" method="POST" action="{{ route('projects.destroy', $project) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Delete this project?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No projects yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
