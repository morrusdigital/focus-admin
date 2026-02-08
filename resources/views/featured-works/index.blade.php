@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Featured Work</h4>
        <a class="btn btn-primary" href="{{ route('featured-works.create') }}">Add Item</a>
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
                            <th>Image</th>
                            <th>Sector</th>
                            <th>Title</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($featuredWorks as $featuredWork)
                            <tr>
                                <td>
                                    @if ($featuredWork->image_url)
                                        <img src="{{ $featuredWork->image_url }}" alt="{{ $featuredWork->title }}" style="height: 40px;" class="img-fluid rounded">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $featuredWork->sector_label }}</div>
                                    <small class="text-muted">{{ $featuredWork->sector_slug }}</small>
                                </td>
                                <td>{{ $featuredWork->title }}</td>
                                <td>{{ $featuredWork->sort_order }}</td>
                                <td>
                                    @if ($featuredWork->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('featured-works.show', $featuredWork) }}">Detail</a>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('featured-works.edit', $featuredWork) }}">Edit</a>
                                    <form class="d-inline" method="POST" action="{{ route('featured-works.destroy', $featuredWork) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Delete this item?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No featured work yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
