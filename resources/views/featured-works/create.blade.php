@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Add Featured Work</h4>
        <a class="btn btn-light" href="{{ route('featured-works.index') }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('featured-works.store') }}" enctype="multipart/form-data">
                @csrf
                @include('featured-works._form')
                <button class="btn btn-primary" type="submit">Save</button>
            </form>
        </div>
    </div>
@endsection
