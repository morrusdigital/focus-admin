@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Article Detail</h4>
        <a class="btn btn-light" href="{{ route('news.index') }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <h5 class="mb-2">{{ $article->title }}</h5>
                    <p class="text-muted">{{ $article->excerpt }}</p>
                    <div class="mb-2"><strong>Category:</strong> {{ $article->category ?? '-' }}</div>
                    <div class="mb-2"><strong>Author:</strong> {{ $article->author ?? '-' }}</div>
                    <div class="mb-2"><strong>Status:</strong> {{ ucfirst($article->status) }}</div>
                    <div class="mb-2"><strong>Published At:</strong> {{ $article->published_at?->format('Y-m-d H:i') ?? '-' }}</div>
                    <div class="mb-3"><strong>Tags:</strong> {{ $article->tags ? implode(', ', $article->tags) : '-' }}</div>
                    <div class="border rounded p-3 bg-light-subtle">
                        {!! $article->content !!}
                    </div>
                </div>
                <div class="col-lg-4">
                    @if ($article->cover_image_url)
                        <img class="img-fluid rounded" src="{{ $article->cover_image_url }}" alt="{{ $article->title }}">
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
