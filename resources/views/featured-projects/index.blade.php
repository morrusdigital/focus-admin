@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Featured Projects</h4>
        <a class="btn btn-primary" href="{{ route('featured-projects.create') }}">Add Project</a>
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
                            <th>Title</th>
                            <th>Scope</th>
                            <th>Size</th>
                            <th>Image</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            <tr>
                                <td>{{ $project->title }}</td>
                                <td>{{ $project->scope }}</td>
                                <td>{{ $project->size }}</td>
                                <td>
                                    <a href="{{ $project->image_url }}" target="_blank" rel="noopener">View</a>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('featured-projects.show', $project) }}">Detail</a>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('featured-projects.edit', $project) }}">Edit</a>
                                    <form class="d-inline" method="POST" action="{{ route('featured-projects.destroy', $project) }}">
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
