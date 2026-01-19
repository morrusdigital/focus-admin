@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Edit Article</h4>
        <a class="btn btn-light" href="{{ route('news.index') }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('news.update', $article) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('news._form', ['article' => $article])
                <button class="btn btn-primary" type="submit">Update</button>
            </form>
        </div>
    </div>
@endsection
