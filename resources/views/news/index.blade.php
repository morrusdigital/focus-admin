@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">News</h4>
        <a class="btn btn-primary" href="{{ route('news.create') }}">Add Article</a>
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
                            <th>Status</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Published At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($news as $article)
                            <tr>
                                <td>{{ $article->title }}</td>
                                <td>
                                    @if ($article->status === 'published')
                                        <span class="badge bg-success">Published</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $article->category ?? '-' }}</td>
                                <td>{{ $article->author ?? '-' }}</td>
                                <td>{{ $article->published_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('news.show', $article) }}">Detail</a>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('news.edit', $article) }}">Edit</a>
                                    <form class="d-inline" method="POST" action="{{ route('news.destroy', $article) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Delete this article?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No news yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
